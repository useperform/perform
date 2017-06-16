<?php

namespace Perform\BaseBundle\Tests\Action;

use Doctrine\ORM\EntityManagerInterface;
use Perform\BaseBundle\Action\ActionRunner;
use Doctrine\Common\Persistence\ObjectRepository;
use Perform\BaseBundle\Action\ActionResponse;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Perform\BaseBundle\Action\ActionConfig;
use Perform\BaseBundle\Action\ConfiguredAction;
use Perform\BaseBundle\Config\ConfigStoreInterface;

/**
 * ActionRunnerTest.
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
    protected $config;

    public function setUp()
    {
        $this->repo = $this->getMock(ObjectRepository::class);
        $this->em = $this->getMock(EntityManagerInterface::class);
        $this->em->expects($this->any())
            ->method('getRepository')
            ->with('FooBundle\\Foo')
            ->will($this->returnValue($this->repo));
        $this->action = $this->getMockBuilder(ConfiguredAction::class)
                        ->disableOriginalConstructor()
                        ->getMock();
        $this->config = $this->getMockBuilder(ActionConfig::class)
                        ->disableOriginalConstructor()
                        ->getMock();
        $this->store = $this->getMock(ConfigStoreInterface::class);

        $this->runner = new ActionRunner($this->em, $this->store);
    }

    public function testRun()
    {
        $actionName = 'foo_action';
        $this->store->expects($this->any())
            ->method('getActionConfig')
            ->with('FooBundle\\Foo')
            ->will($this->returnValue($this->config));
        $this->config->expects($this->any())
            ->method('get')
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
            ->with([$entity])
            ->will($this->returnValue($response));

        $this->assertSame($response, $this->runner->run($actionName, 'FooBundle\\Foo', ['some-id'], []));
    }

    public function testRunNotGranted()
    {
        $actionName = 'foo_action';
        $this->store->expects($this->any())
            ->method('getActionConfig')
            ->with('FooBundle\\Foo')
            ->will($this->returnValue($this->config));
        $this->config->expects($this->any())
            ->method('get')
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

        $this->runner->run($actionName, 'FooBundle\\Foo', ['some-id'], []);
    }
}
