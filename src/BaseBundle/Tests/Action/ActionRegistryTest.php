<?php

namespace Perform\BaseBundle\Tests\Action;

use Perform\BaseBundle\Action\ActionRegistry;
use Perform\BaseBundle\Action\ActionInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Perform\BaseBundle\Action\ActionNotFoundException;

/**
 * ActionRegistryTest
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class ActionRegistryTest extends \PHPUnit_Framework_TestCase
{
    protected $container;
    protected $registry;

    public function setUp()
    {
        $this->container = $this->getMock(ContainerInterface::class);
        $this->registry = new ActionRegistry($this->container, ['foo_action' => 'foo_service']);
    }

    public function testGetAction()
    {
        $action = $this->getMock(ActionInterface::class);
        $this->container->expects($this->any())
            ->method('get')
            ->with('foo_service')
            ->will($this->returnValue($action));

        $this->assertSame($action, $this->registry->getAction('foo_action'));
    }

    public function testGetUnknownService()
    {
        $this->setExpectedException(ActionNotFoundException::class);
        $this->registry->getAction('bar_action');
    }
}
