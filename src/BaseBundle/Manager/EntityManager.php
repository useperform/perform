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
}
