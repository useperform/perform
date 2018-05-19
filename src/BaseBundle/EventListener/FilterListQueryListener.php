<?php

namespace Perform\BaseBundle\EventListener;

use Doctrine\ORM\QueryBuilder;
use Perform\BaseBundle\Config\ConfigStoreInterface;
use Perform\BaseBundle\Crud\CrudRequest;
use Perform\BaseBundle\Event\ListQueryEvent;
use Perform\BaseBundle\Selector\EntitySelector;
use Perform\BaseBundle\Event\ContextEvent;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class FilterListQueryListener
{
    protected $store;
    protected $selector;

    public function __construct(ConfigStoreInterface $store, EntitySelector $selector)
    {
        $this->store = $store;
        $this->selector = $selector;
    }

    public function onListQuery(ListQueryEvent $event)
    {
        $request = $event->getCrudRequest();
        $entityClass = $request->getEntityClass();
        $filterName = $request->getFilter();
        if (!$filterName) {
            return;
        }
        $filterConfig = $this->store->getFilterConfig($entityClass);
        $filter = $filterConfig->getFilter($filterName);
        if (!$filter) {
            return;
        }

        $func = $filter->getConfig()['query'];
        if (!is_callable($func)) {
            return;
        }

        $newQb = $func($event->getQueryBuilder());
        if (!$newQb) {
            return;
        }
        if (!$newQb instanceof QueryBuilder) {
            throw new \UnexpectedValueException(sprintf('The filter function "%s" for %s must return an instance of Doctrine\ORM\QueryBuilder or null.', $filterName, $entityClass));
        }

        $event->setQueryBuilder($newQb);
    }

    public function onListContext(ContextEvent $event)
    {
        $request = $event->getCrudRequest();
        $entityClass = $request->getEntityClass();
        $filterConfig = $this->store->getFilterConfig($entityClass);
        if (!$filterConfig) {
            return;
        }

        // For every filter:
        //
        // Mark it as active if required
        //
        // If counting is enabled, run another query with each filter
        // applied, modified to only select the row count
        foreach ($filterConfig->getFilters() as $filterName => $filter) {
            if ($request->getFilter() === $filterName) {
                $filter->setActive(true);
            }

            $config = $filter->getConfig();
            if (!$config['count']) {
                continue;
            }
            $childRequest = clone $request;
            $childRequest->setFilter($filterName);
            $qb = $this->selector->getQueryBuilder($childRequest);
            $count = $qb->select('COUNT(1)')
                   ->getQuery()
                   ->getSingleScalarResult();
            $filter->setCount($count);
        }
    }
}
