<?php

namespace Perform\BaseBundle\Selector;

use Pagerfanta\Pagerfanta;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Perform\BaseBundle\Config\TypeConfig;
use Perform\BaseBundle\Config\FilterConfig;
use Perform\BaseBundle\Crud\CrudRequest;
use Perform\BaseBundle\Config\ConfigStoreInterface;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class EntitySelector
{
    protected $entityManager;
    protected $store;

    public function __construct(EntityManagerInterface $entityManager, ConfigStoreInterface $store)
    {
        $this->entityManager = $entityManager;
        $this->store = $store;
    }

    public function getQueryBuilder(CrudRequest $request)
    {
        return $this->getQueryBuilderInternal($request)[0];
    }

    private function getQueryBuilderInternal(CrudRequest $request)
    {
        $entityName = $request->getEntityClass();
        if (!$entityName) {
            throw new \InvalidArgumentException('Missing required entity class.');
        }

        $qb = $this->entityManager->createQueryBuilder()
            ->select('e')
            ->from($entityName, 'e');

        //potentially add sorting, using custom functions from TypeConfig
        $defaultSort = $this->store->getTypeConfig($entityName)->getDefaultSort();
        $orderField = $request->getSortField($defaultSort[0]);
        $direction = $request->getSortDirection($defaultSort[1]);

        //direction can be set to 'N' to override default sorting
        if ($direction === 'N') {
            $orderField = null;
        }
        $qb = $this->maybeOrderBy($qb, $entityName, $orderField, $direction);
        if (!$qb instanceof QueryBuilder) {
            throw new \UnexpectedValueException(sprintf('The sort function for %s->%s must return an instance of Doctrine\ORM\QueryBuilder.', $entityName, $orderField));
        }

        //potentially add filtering, using FilterConfig from the
        //admin, or even returning a new builder entirely
        $filterName = $request->getFilter($this->store->getFilterConfig($entityName)->getDefault());

        $qb = $this->maybeFilter($qb, $entityName, $filterName, true);
        if (!$qb instanceof QueryBuilder) {
            throw new \UnexpectedValueException(sprintf('The filter function "%s" for %s must return an instance of Doctrine\ORM\QueryBuilder.', $filterName, $entityName));
        }

        return [$qb, $orderField, $direction];
    }

    public function listContext(CrudRequest $request)
    {
        list($qb, $orderField, $direction) = $this->getQueryBuilderInternal($request);
        $this->assignFilterCounts($request->getEntityClass());

        $paginator = new Pagerfanta(new DoctrineORMAdapter($qb));
        $paginator->setMaxPerPage(10);
        $paginator->setCurrentPage($request->getPage());

        $orderBy = [
            'field' => $orderField,
            'direction' => $direction,
        ];

        return [$paginator, $orderBy];
    }

    protected function assignFilterCounts($entityName)
    {
        $filterConfig = $this->store->getFilterConfig($entityName);
        if (!$filterConfig) {
            return;
        }

        foreach ($filterConfig->getFilters() as $filterName => $filter) {
            $config = $filter->getConfig();
            if (!$config['count']) {
                continue;
            }
            $qb = $this->entityManager->createQueryBuilder()
                ->select('COUNT(1)')
                ->from($entityName, 'e');
            $this->maybeFilter($qb, $entityName, $filterName);
            $count = $qb->getQuery()->getSingleScalarResult();
            $filter->setCount($count);
        }
    }

    /**
     * @return QueryBuilder
     */
    protected function maybeOrderBy(QueryBuilder $qb, $entityName, $orderField, $direction)
    {
        if (!$orderField) {
            return $qb;
        }

        $typeConfig = $this->store->getTypeConfig($entityName)->getTypes(TypeConfig::CONTEXT_LIST);
        if (!isset($typeConfig[$orderField]['sort'])) {
            // no type config available for this field, but assume they want to sort by the doctrine field supplied
            return $qb->orderBy('e.'.$orderField, $direction);
        }
        $sort = $typeConfig[$orderField]['sort'];

        if ($sort === true) {
            return $qb->orderBy('e.'.$orderField, $direction);
        }
        if (is_callable($sort)) {
            return $sort($qb, $direction);
        }

        return $qb;
    }

    /**
     * @return QueryBuilder
     */
    protected function maybeFilter(QueryBuilder $qb, $entityName, $filterName, $active = false)
    {
        if (!$filterName) {
            return $qb;
        }

        $filter = $this->store->getFilterConfig($entityName)->getFilter($filterName);
        if (!$filter) {
            return $qb;
        }

        if ($active) {
            $filter->setActive(true);
        }

        $config = $filter->getConfig();
        $filteredQuery = $config['query']($qb);

        return $filteredQuery;
    }
}
