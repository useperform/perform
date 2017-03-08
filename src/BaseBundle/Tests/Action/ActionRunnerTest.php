<?php

namespace Perform\BaseBundle\Tests\Action;

use Doctrine\ORM\EntityManagerInterface;
use Perform\BaseBundle\Action\ActionRunner;
use Perform\BaseBundle\Action\ActionInterface;
use Doctrine\Common\Persistence\ObjectRepository;
use Perform\BaseBundle\Action\ActionRegistry;
use Perform\BaseBundle\Action\ActionResponse;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * ActionRunnerTest
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class ActionRunnerTest extends \PHPUnit_Framework_TestCase
{
    protected $em;
    protected $repo;
    protected $action;
    protected $registry;
    protected $runner;

    public function setUp()
    {
        $this->repo = $this->getMock(ObjectRepository::class);
        $this->em = $this->getMock(EntityManagerInterface::class);
        $this->em->expects($this->any())
            ->method('getRepository')
            ->with('FooBundle:Foo')
            ->will($this->returnValue($this->repo));
        $this->action = $this->getMock(ActionInterface::class);
        $this->registry = $this->getMockBuilder(ActionRegistry::class)
                        ->disableOriginalConstructor()
                        ->getMock();

        $this->runner = new ActionRunner($this->em, $this->registry);
    }

    public function testRun()
    {
        $actionName = 'foo_action';
        $this->action->expects($this->any())
            ->method('getTargetEntity')
            ->will($this->returnValue('FooBundle:Foo'));
        $this->registry->expects($this->any())
            ->method('getAction')
            ->with('foo_action')
            ->will($this->returnValue($this->action));
        $entity = new \stdClass();
        $this->repo->expects($this->any())
            ->method('find')
            ->with('some-id')
            ->will($this->returnValue($entity));
        $response = new ActionResponse('success');
        $this->action->expects($this->once())
            ->method('isGranted')
            ->with($entity)
            ->will($this->returnValue(true));
        $this->action->expects($this->once())
            ->method('run')
            ->with($entity, [])
            ->will($this->returnValue($response));

        $this->assertSame($response, $this->runner->run($actionName, 'some-id', []));
    }

    public function testRunNotGranted()
    {
        $actionName = 'foo_action';
        $this->action->expects($this->any())
            ->method('getTargetEntity')
            ->will($this->returnValue('FooBundle:Foo'));
        $this->registry->expects($this->any())
            ->method('getAction')
            ->with('foo_action')
            ->will($this->returnValue($this->action));
        $entity = new \stdClass();
        $this->repo->expects($this->any())
            ->method('find')
            ->with('some-id')
            ->will($this->returnValue($entity));
        $this->action->expects($this->never())
            ->method('run');
        $this->setExpectedException(AccessDeniedException::class);

        $this->runner->run($actionName, 'some-id', []);
    }
}
