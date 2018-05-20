<?php

namespace Perform\BaseBundle\Tests\Manager;

use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Doctrine\ORM\EntityManagerInterface;
use Perform\BaseBundle\Event\EntityEvent;
use Perform\BaseBundle\Manager\EntityManager;
use Perform\BaseBundle\Crud\CrudRequest;

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
        $eventCallback = function ($e) {
            return $e instanceof EntityEvent;
        };
        $this->dispatcher->expects($this->exactly(2))
            ->method('dispatch')
            ->withConsecutive(
                [$this->equalTo(EntityEvent::PRE_CREATE), $this->callback($eventCallback)],
                [$this->equalTo(EntityEvent::POST_CREATE), $this->callback($eventCallback)]
            );

        $this->assertSame($entity, $this->manager->create(new CrudRequest(CrudRequest::CONTEXT_CREATE), $entity));
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
            ->will($this->returnCallback(function ($type, $event) use ($newEntity) {
                $event->setEntity($newEntity);
            }));

        $this->assertSame($newEntity, $this->manager->create(new CrudRequest(CrudRequest::CONTEXT_CREATE), $entity));
    }

    public function testUpdate()
    {
        $entity = new \stdClass();
        $this->em->expects($this->once())
            ->method('persist')
            ->with($entity);
        $this->em->expects($this->once())
            ->method('flush');
        $eventCallback = function ($e) {
            return $e instanceof EntityEvent;
        };
        $this->dispatcher->expects($this->exactly(2))
            ->method('dispatch')
            ->withConsecutive(
                [$this->equalTo(EntityEvent::PRE_UPDATE), $this->callback($eventCallback)],
                [$this->equalTo(EntityEvent::POST_UPDATE), $this->callback($eventCallback)]
            );

        $this->assertSame($entity, $this->manager->update(new CrudRequest(CrudRequest::CONTEXT_EDIT), $entity));
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
            ->will($this->returnCallback(function ($type, $event) use ($newEntity) {
                $event->setEntity($newEntity);
            }));

        $this->assertSame($newEntity, $this->manager->update(new CrudRequest(CrudRequest::CONTEXT_EDIT), $entity));
    }

    public function testDelete()
    {
        $entity = new \stdClass();
        $this->em->expects($this->once())
            ->method('remove')
            ->with($entity);
        $this->em->expects($this->once())
            ->method('flush');
        $eventCallback = function ($e) {
            return $e instanceof EntityEvent;
        };
        $this->dispatcher->expects($this->exactly(2))
            ->method('dispatch')
            ->withConsecutive(
                [$this->equalTo(EntityEvent::PRE_DELETE), $this->callback($eventCallback)],
                [$this->equalTo(EntityEvent::POST_DELETE), $this->callback($eventCallback)]
            );

        $this->assertSame($entity, $this->manager->delete($entity));
    }

    public function testDeleteWithChangedEntity()
    {
        $entity = new \stdClass();
        $newEntity = new \stdClass();
        $this->em->expects($this->once())
            ->method('remove')
            ->with($this->identicalTo($newEntity));
        $this->dispatcher->expects($this->any())
            ->method('dispatch')
            ->will($this->returnCallback(function ($type, $event) use ($newEntity) {
                $event->setEntity($newEntity);
            }));

        $this->assertSame($newEntity, $this->manager->delete($entity));
    }

    public function testDeleteMany()
    {
        $one = new \stdClass();
        $two = new \stdClass();
        $this->em->expects($this->exactly(2))
            ->method('remove')
            ->with($this->logicalOr($one, $two));
        $this->em->expects($this->once())
            ->method('flush');
        $eventCallback = function ($e) {
            return $e instanceof EntityEvent;
        };
        $this->dispatcher->expects($this->exactly(4))
            ->method('dispatch')
            ->withConsecutive(
                [$this->equalTo(EntityEvent::PRE_DELETE), $this->callback($eventCallback)],
                [$this->equalTo(EntityEvent::PRE_DELETE), $this->callback($eventCallback)],
                [$this->equalTo(EntityEvent::POST_DELETE), $this->callback($eventCallback)],
                [$this->equalTo(EntityEvent::POST_DELETE), $this->callback($eventCallback)]
            );

        $this->assertSame([$one, $two], $this->manager->deleteMany([$one, $two]));
    }

    public function testDeleteManyWithChangedEntity()
    {
        $one = new \stdClass();
        $newOne = new \stdClass();
        $two = new \stdClass();
        $newTwo = new \stdClass();
        $this->em->expects($this->exactly(2))
            ->method('remove')
            ->with($this->logicalOr($newOne, $newTwo));
        $this->dispatcher->expects($this->any())
            ->method('dispatch')
            ->will($this->returnCallback(function ($type, $event) use ($one, $newOne, $newTwo) {
                if ($type === EntityEvent::PRE_DELETE) {
                    $newEntity = $event->getEntity() === $one ? $newOne : $newTwo;
                    $event->setEntity($newEntity);
                }
            }));

        $this->assertSame([$newOne, $newTwo], $this->manager->deleteMany([$one, $two]));
    }
}
