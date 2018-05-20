<?php

namespace Perform\BaseBundle\Manager;

use Perform\BaseBundle\Event\EntityEvent;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Psr\Log\LoggerInterface;
use Perform\BaseBundle\Crud\CrudRequest;

/**
 * Wrapper for the doctrine entity manager that dispatches events
 * before and after database interactions.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class EntityManager
{
    protected $em;
    protected $dispatcher;
    protected $logger;

    public function __construct(EntityManagerInterface $em, EventDispatcherInterface $dispatcher, LoggerInterface $logger)
    {
        $this->em = $em;
        $this->dispatcher = $dispatcher;
        $this->logger = $logger;
    }

    /**
     * Save a new entity.
     */
    public function create(CrudRequest $crudRequest, $entity)
    {
        try {
            $preCreate = new EntityEvent($crudRequest, $entity);
            $this->dispatcher->dispatch(EntityEvent::PRE_CREATE, $preCreate);
            // entity may have been replaced by the event
            $newEntity = $preCreate->getEntity();
            $this->em->persist($newEntity);
            $this->em->flush();
            $postCreate = new EntityEvent($crudRequest, $newEntity);
            $this->dispatcher->dispatch(EntityEvent::POST_CREATE, $postCreate);

            return $newEntity;
        } catch (\Exception $e) {
            $this->logger->error('An exception occurred creating an entity.', [
                'entity' => $entity,
                'error' => $e,
            ]);
            throw $e;
        }
    }

    /**
     * Update an existing entity.
     */
    public function update(CrudRequest $crudRequest, $entity)
    {
        try {
            $preUpdate = new EntityEvent($crudRequest, $entity);
            $this->dispatcher->dispatch(EntityEvent::PRE_UPDATE, $preUpdate);
            // entity may have been replaced by the event
            $newEntity = $preUpdate->getEntity();
            $this->em->persist($newEntity);
            $this->em->flush();
            $postUpdate = new EntityEvent($crudRequest, $newEntity);
            $this->dispatcher->dispatch(EntityEvent::POST_UPDATE, $postUpdate);

            return $newEntity;
        } catch (\Exception $e) {
            $this->logger->error('An exception occurred updating an entity.', [
                'entity' => $entity,
                'error' => $e,
            ]);
            throw $e;
        }
    }

    /**
     * Delete an entity from the database.
     *
     * @param object $entity
     */
    public function delete($entity)
    {
        $crudRequest = new CrudRequest(CrudRequest::CONTEXT_DELETE);
        $crudRequest->setEntityClass(get_class($entity));
        $preDelete = new EntityEvent($crudRequest, $entity);
        $this->dispatcher->dispatch(EntityEvent::PRE_DELETE, $preDelete);
        // entity may have been replaced by the event
        $newEntity = $preDelete->getEntity();
        $this->em->remove($newEntity);
        $this->em->flush();
        $postDelete = new EntityEvent($crudRequest, $newEntity);
        $this->dispatcher->dispatch(EntityEvent::POST_DELETE, $postDelete);

        return $newEntity;
    }

    /**
     * Delete an array of entities from the database in a single transaction.
     *
     * Note that all of the PRE_DELETE events will be dispatched
     * first, followed by all of the POST_DELETE events.
     *
     * @param array $entities
     */
    public function deleteMany(array $entities)
    {
        $deletedEntities = [];
        $postDeletes = [];

        foreach ($entities as $entity) {
            $crudRequest = new CrudRequest(CrudRequest::CONTEXT_DELETE);
            $crudRequest->setEntityClass(get_class($entity));
            $preDelete = new EntityEvent($crudRequest, $entity);
            $this->dispatcher->dispatch(EntityEvent::PRE_DELETE, $preDelete);
            // entity may have been replaced by the event
            $newEntity = $preDelete->getEntity();
            $this->em->remove($newEntity);
            $deletedEntities[] = $newEntity;
            $postDeletes[] = new EntityEvent($crudRequest, $newEntity);
        }

        $this->em->flush();

        foreach ($postDeletes as $event) {
            $this->dispatcher->dispatch(EntityEvent::POST_DELETE, $event);
        }

        return $deletedEntities;
    }
}
