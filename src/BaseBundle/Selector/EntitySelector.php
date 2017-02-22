<?php

namespace Perform\BaseBundle\Selector;

use Pagerfanta\Pagerfanta;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Symfony\Component\HttpFoundation\Request;
use Perform\BaseBundle\Admin\AdminRegistry;
use Doctrine\ORM\EntityManagerInterface;
use Perform\BaseBundle\Type\EntityTypeConfig;
use Doctrine\ORM\QueryBuilder;
use Perform\BaseBundle\Type\TypeConfig;
use Perform\BaseBundle\Filter\FilterConfig;

/**
 * EntitySelector.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class EntitySelector
{
    protected $entityManager;
    protected $registry;

    public function __construct(EntityManagerInterface $entityManager, AdminRegistry $registry)
    {
        $this->entityManager = $entityManager;
        $this->registry = $registry;
    }

    public function listContext(Request $request, $entityName)
    {
        $qb = $this->entityManager->createQueryBuilder()
            ->select('e')
            ->from($entityName, 'e');

        //potentially add sorting, using custom functions from TypeConfig
        $defaultSort = $this->registry->getTypeConfig($entityName)->getDefaultSort();
        $orderField = $request->query->get('sort', $defaultSort[0]);
        $direction = strtoupper($request->query->get('direction', $defaultSort[1]));
        if ($direction !== 'DESC' && $direction !== 'N') {
            $direction = 'ASC';
        }
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
        $filterName = $request->query->get('filter', $this->registry->getFilterConfig($entityName)->getDefault());

        $qb = $this->maybeFilter($qb, $entityName, $filterName, true);
        if (!$qb instanceof QueryBuilder) {
            throw new \UnexpectedValueException(sprintf('The filter function "%s" for %s must return an instance of Doctrine\ORM\QueryBuilder.', $filterName, $entityName));
        }

        $this->assignFilterCounts($entityName);

        $paginator = new Pagerfanta(new DoctrineORMAdapter($qb));
        $paginator->setMaxPerPage(10);
        $paginator->setCurrentPage($request->query->get('page', 1));

        $orderBy = [
            'field' => $orderField,
            'direction' => $direction,
        ];

        return [$paginator, $orderBy];
    }

    protected function assignFilterCounts($entityName)
    {
        $filterConfig = $this->registry->getFilterConfig($entityName);
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

        $typeConfig = $this->registry->getTypeConfig($entityName)->getTypes(TypeConfig::CONTEXT_LIST);
        if (!isset($typeConfig[$orderField]['sort'])) {
            return $qb;
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

        $filter = $this->registry->getFilterConfig($entityName)->getFilter($filterName);
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
