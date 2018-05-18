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
        $entityClass = $this->getEntityClass($request);
        $qb = $this->entityManager->createQueryBuilder()
            ->select('e')
            ->from($entityClass, 'e');

        $event = new ListQueryEvent($qb, $request);
        $this->dispatcher->dispatch(ListQueryEvent::NAME, $event);

        return $event->getQueryBuilder();
    }

    public function listContext(CrudRequest $request)
    {
        $entityClass = $this->getEntityClass($request);
        $request->setDefaultFilter($this->store->getFilterConfig($entityClass)->getDefault());
        $defaultSort = $this->store->getTypeConfig($entityClass)->getDefaultSort();
        $request->setDefaultSortField($defaultSort[0]);
        $request->setDefaultSortDirection($defaultSort[1]);

        $qb = $this->getQueryBuilder($request);

        $paginator = new Pagerfanta(new DoctrineORMAdapter($qb));
        $paginator->setMaxPerPage(10);
        $paginator->setCurrentPage($request->getPage());

        $orderBy = [
            'field' => $request->getSortField(),
            'direction' => $request->getSortDirection(),
        ];

        return [$paginator, $orderBy];
    }

    private function getEntityClass(CrudRequest $request)
    {
        $entityClass = $request->getEntityClass();
        if (!$entityClass) {
            throw new \InvalidArgumentException('Missing required entity class.');
        }

        return $entityClass;
    }
}
