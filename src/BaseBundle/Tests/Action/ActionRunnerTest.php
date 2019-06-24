<?php

namespace Perform\BaseBundle\Tests\Action;

use PHPUnit\Framework\TestCase;
use Doctrine\ORM\EntityManagerInterface;
use Perform\BaseBundle\Action\ActionRunner;
use Doctrine\Common\Persistence\ObjectRepository;
use Perform\BaseBundle\Action\ActionResponse;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Perform\BaseBundle\Config\ActionConfig;
use Perform\BaseBundle\Action\ConfiguredAction;
use Perform\BaseBundle\Config\ConfigStoreInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Perform\BaseBundle\Crud\CrudRequest;

/**
 * ActionRunnerTest.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class ActionRunnerTest extends TestCase
{
    protected $em;
    protected $repo;
    protected $action;
    protected $registry;
    protected $runner;
    protected $config;

    public function setUp()
    {
        $this->repo = $this->createMock(ObjectRepository::class);
        $this->em = $this->createMock(EntityManagerInterface::class);
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
        $this->store = $this->createMock(ConfigStoreInterface::class);
        $this->store->expects($this->any())
            ->method('getEntityClass')
            ->with('some_crud')
            ->will($this->returnValue('FooBundle\\Foo'));

        $this->authChecker = $this->createMock(AuthorizationCheckerInterface::class);

        $this->runner = new ActionRunner($this->em, $this->store, $this->authChecker);
    }

    public function testRun()
    {
        $actionName = 'foo_action';
        $crudName = 'some_crud';
        $this->store->expects($this->any())
            ->method('getActionConfig')
            ->with($crudName)
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
            ->with($entity, $this->authChecker)
            ->will($this->returnValue(true));
        $options = [
            'some_option' => true,
            'other_options' => false,
        ];
        $this->action->expects($this->once())
            ->method('run')
            ->with(
                $this->callback(function($request) use ($crudName) {
                    return $request instanceof CrudRequest
                        && $request->getCrudName() === $crudName
                        && $request->getContext() === CrudRequest::CONTEXT_ACTION;
                }),
                [$entity], $options)
            ->will($this->returnValue($response));

        $this->assertSame($response, $this->runner->run($crudName, $actionName, ['some-id'], $options));
    }

    public function testRunNotGranted()
    {
        $crudName = 'some_crud';
        $actionName = 'foo_action';
        $this->store->expects($this->any())
            ->method('getActionConfig')
            ->with($crudName)
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
        $this->expectException(AccessDeniedException::class);

        $this->runner->run($crudName, $actionName, ['some-id'], []);
    }
}
