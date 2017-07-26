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
}
