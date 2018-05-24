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
use Perform\BaseBundle\Event\QueryEvent;

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
        $entityClass = $this->store->getEntityClass($request->getCrudName());
        $qb = $this->entityManager->createQueryBuilder()
            ->select('e')
            ->from($entityClass, 'e');

        $event = new QueryEvent($qb, $request);
        $this->dispatcher->dispatch(QueryEvent::LIST_QUERY, $event);

        return $event->getQueryBuilder();
    }

    public function listContext(CrudRequest $request)
    {
        $crudName = $request->getCrudName();
        $request->setDefaultFilter($this->store->getFilterConfig($crudName)->getDefault());
        $defaultSort = $this->store->getTypeConfig($crudName)->getDefaultSort();
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

    public function viewContext(CrudRequest $request, $identifier)
    {
        return $this->selectSingleEntity($request, $identifier, QueryEvent::VIEW_QUERY);
    }

    public function editContext(CrudRequest $request, $identifier)
    {
        return $this->selectSingleEntity($request, $identifier, QueryEvent::EDIT_QUERY);
    }

    private function selectSingleEntity(CrudRequest $request, $identifier, $eventName)
    {
        $entityClass = $this->store->getEntityClass($request->getCrudName());
        $idColumn = $this->entityManager->getClassMetadata($entityClass)->getIdentifier();

        if (!is_array($idColumn) || !isset($idColumn[0])) {
            throw new \Exception('Only non-composite doctrine identifiers are supported.');
        }

        $qb = $this->entityManager->createQueryBuilder()
            ->select('e')
            ->from($entityClass, 'e')
            ->where(sprintf('e.%s = :identifier', $idColumn[0]))
            ->setParameter('identifier', $identifier);

        $event = new QueryEvent($qb, $request);
        $this->dispatcher->dispatch($eventName, $event);

        $result = $event->getQueryBuilder()
                ->getQuery()
                ->setMaxResults(1)
                ->getResult();

        return isset($result[0]) ? $result[0] : null;
    }
}
