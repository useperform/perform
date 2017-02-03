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
        $orderField = $request->query->get('sort', null);
        $direction = strtoupper($request->query->get('direction', 'asc'));
        if ($direction !== 'DESC') {
            $direction = 'ASC';
        }
        $qb = $this->maybeOrderBy($qb, $entityName, $orderField, $direction);
        if (!$qb instanceof QueryBuilder) {
            throw new \UnexpectedValueException(sprintf('The sort function for %s->%s must return an instance of Doctrine\ORM\QueryBuilder.', $entityName, $orderField));
        }

        //potentially add filtering, using FilterConfig from the
        //admin, or even returning a new builder entirely
        $filterName = $request->query->get('filter', null);

        $qb = $this->maybeFilter($qb, $entityName, $filterName);
        if (!$qb instanceof QueryBuilder) {
            throw new \UnexpectedValueException(sprintf('The filter function "%s" for %s must return an instance of Doctrine\ORM\QueryBuilder.', $filterName, $entityName));
        }

        $paginator = new Pagerfanta(new DoctrineORMAdapter($qb));
        $paginator->setMaxPerPage(10);
        $paginator->setCurrentPage($request->query->get('page', 1));

        $orderBy = [
            'field' => $orderField,
            'direction' => $direction,
        ];

        return [$paginator, $orderBy];
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
        if (!isset($typeConfig[$orderField]['options']['sort'])) {
            return $qb;
        }
        $sort = $typeConfig[$orderField]['options']['sort'];

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
    protected function maybeFilter(QueryBuilder $qb, $entityName, $filterName)
    {
        if (!$filterName) {
            return $qb;
        }

        $filter = $this->registry->getFilterConfig($entityName)->getFilter($filterName);
        if (!$filter) {
            return $qb;
        }

        $config = $filter->getConfig();
        $filteredQuery = $config['query']($qb);

        return $filteredQuery;
    }
}
