<?php

namespace Perform\BaseBundle\EventListener;

use Doctrine\ORM\QueryBuilder;
use Perform\BaseBundle\Config\ConfigStoreInterface;
use Perform\BaseBundle\Crud\CrudRequest;
use Perform\BaseBundle\Event\ListQueryEvent;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class SortListQueryListener
{
    protected $store;

    public function __construct(ConfigStoreInterface $store)
    {
        $this->store = $store;
    }

    public function onListQuery(ListQueryEvent $event)
    {
        $request = $event->getCrudRequest();
        $entityName = $request->getEntityClass();
        $orderField = $request->getSortField();
        if (!$orderField) {
            return;
        }

        $direction = $request->getSortDirection();
        //direction can be set to 'N' to override default sorting
        if ($direction === 'N') {
            return;
        }

        $typeConfig = $this->store->getTypeConfig($entityName);
        $qb = $event->getQueryBuilder();

        $typeConfig = $typeConfig->getTypes(CrudRequest::CONTEXT_LIST);
        if (!isset($typeConfig[$orderField]['sort'])) {
            // no type config available for $orderField, but assume
            // they want to sort by it anyway since it was supplied.
            $qb->orderBy('e.'.$orderField, $direction);

            return;
        }
        $sort = $typeConfig[$orderField]['sort'];

        if ($sort === true) {
            $qb->orderBy('e.'.$orderField, $direction);

            return;
        }
        if (!is_callable($sort)) {
            return;
        }
        $newQb = $sort($qb, $direction);
        if (!$newQb) {
            return;
        }
        if (!$newQb instanceof QueryBuilder) {
            throw new \UnexpectedValueException(sprintf('The sort function for %s->%s must return an instance of Doctrine\ORM\QueryBuilder or null.', $entityName, $orderField));
        }

        $event->setQueryBuilder($newQb);
    }
}
