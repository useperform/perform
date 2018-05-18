<?php

namespace Perform\BaseBundle\Selector;

use Pagerfanta\Pagerfanta;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Perform\BaseBundle\Config\FilterConfig;
use Perform\BaseBundle\Crud\CrudRequest;
use Perform\BaseBundle\Config\ConfigStoreInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Perform\BaseBundle\Event\ListQueryEvent;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class EntitySelector
{
    protected $entityManager;
    protected $dispatcher;
    protected $store;

    public function __construct(EntityManagerInterface $entityManager, EventDispatcherInterface $dispatcher, ConfigStoreInterface $store)
    {
        $this->entityManager = $entityManager;
        $this->dispatcher = $dispatcher;
        $this->store = $store;
    }

    public function getQueryBuilder(CrudRequest $request)
    {
        $entityName = $request->getEntityClass();
        if (!$entityName) {
            throw new \InvalidArgumentException('Missing required entity class.');
        }

        $qb = $this->entityManager->createQueryBuilder()
            ->select('e')
            ->from($entityName, 'e');

        $this->dispatcher->dispatch(ListQueryEvent::NAME, new ListQueryEvent($qb, $request));

        //potentially add filtering, using FilterConfig from the
        //admin, or even returning a new builder entirely
        $filterName = $request->getFilter($this->store->getFilterConfig($entityName)->getDefault());

        $qb = $this->maybeFilter($qb, $entityName, $filterName, true);
        if (!$qb instanceof QueryBuilder) {
            throw new \UnexpectedValueException(sprintf('The filter function "%s" for %s must return an instance of Doctrine\ORM\QueryBuilder.', $filterName, $entityName));
        }

        // return the query builder from the event
        return $qb;
    }

    public function listContext(CrudRequest $request)
    {
        $qb = $this->getQueryBuilder($request);
        $this->assignFilterCounts($request->getEntityClass());

        $paginator = new Pagerfanta(new DoctrineORMAdapter($qb));
        $paginator->setMaxPerPage(10);
        $paginator->setCurrentPage($request->getPage());

        $orderBy = [
            'field' => $request->getSortField(),
            'direction' => $request->getSortDirection(),
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
