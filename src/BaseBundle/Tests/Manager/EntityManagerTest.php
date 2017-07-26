<?php

namespace Perform\BaseBundle\Tests\Manager;

use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Doctrine\ORM\EntityManagerInterface;
use Perform\BaseBundle\Event\EntityEvent;
use Perform\BaseBundle\Manager\EntityManager;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class EntityManagerTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->em = $this->getMock(EntityManagerInterface::class);
        $this->dispatcher = $this->getMock(EventDispatcherInterface::class);
        $this->logger = $this->getMock(LoggerInterface::class);
        $this->manager = new EntityManager($this->em, $this->dispatcher, $this->logger);
    }

    public function testCreate()
    {
        $entity = new \stdClass();
        $this->em->expects($this->once())
            ->method('persist')
            ->with($entity);
        $this->em->expects($this->once())
            ->method('flush');
        $eventCallback = function($e) {
            return $e instanceof EntityEvent;
        };
        $this->dispatcher->expects($this->exactly(2))
            ->method('dispatch')
            ->withConsecutive(
                [$this->equalTo(EntityEvent::PRE_CREATE), $this->callback($eventCallback)],
                [$this->equalTo(EntityEvent::POST_CREATE), $this->callback($eventCallback)]
            );

        $this->manager->create($entity);
    }

    public function testCreateWithChangedEntity()
    {
        $entity = new \stdClass();
        $newEntity = new \stdClass();
        $this->em->expects($this->once())
            ->method('persist')
            ->with($this->identicalTo($newEntity));
        $this->dispatcher->expects($this->any())
            ->method('dispatch')
            ->will($this->returnCallback(function($type, $event) use ($newEntity) {
                $event->setEntity($newEntity);
            }));

        $this->manager->create($entity);
    }

    public function testUpdate()
    {
        $entity = new \stdClass();
        $this->em->expects($this->once())
            ->method('persist')
            ->with($entity);
        $this->em->expects($this->once())
            ->method('flush');
        $eventCallback = function($e) {
            return $e instanceof EntityEvent;
        };
        $this->dispatcher->expects($this->exactly(2))
            ->method('dispatch')
            ->withConsecutive(
                [$this->equalTo(EntityEvent::PRE_UPDATE), $this->callback($eventCallback)],
                [$this->equalTo(EntityEvent::POST_UPDATE), $this->callback($eventCallback)]
            );

        $this->manager->update($entity);
    }

    public function testUpdateWithChangedEntity()
    {
        $entity = new \stdClass();
        $newEntity = new \stdClass();
        $this->em->expects($this->once())
            ->method('persist')
            ->with($this->identicalTo($newEntity));
        $this->dispatcher->expects($this->any())
            ->method('dispatch')
            ->will($this->returnCallback(function($type, $event) use ($newEntity) {
                $event->setEntity($newEntity);
            }));

        $this->manager->update($entity);
    }
}
