<?php

namespace Perform\BaseBundle\Manager;

use Perform\BaseBundle\Event\EntityEvent;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Psr\Log\LoggerInterface;

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
    public function create($entity)
    {
        try {
            $event = new EntityEvent($entity);
            $this->dispatcher->dispatch(EntityEvent::PRE_CREATE, $event);
            $this->em->persist($event->getEntity());
            $this->em->flush();
            $this->dispatcher->dispatch(EntityEvent::POST_CREATE, new EntityEvent($entity));
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
    public function update($entity)
    {
        try {
            $event = new EntityEvent($entity);
            $this->dispatcher->dispatch(EntityEvent::PRE_UPDATE, $event);
            $this->em->persist($event->getEntity());
            $this->em->flush();
            $this->dispatcher->dispatch(EntityEvent::POST_UPDATE, new EntityEvent($entity));
        } catch (\Exception $e) {
            $this->logger->error('An exception occurred updating an entity.', [
                'entity' => $entity,
                'error' => $e,
            ]);
            throw $e;
        }
    }
}
