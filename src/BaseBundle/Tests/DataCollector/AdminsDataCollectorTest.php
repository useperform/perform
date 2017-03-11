<?php

namespace Perform\BaseBundle\Tests\DataCollector;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Perform\BaseBundle\Type\TypeRegistry;
use Perform\BaseBundle\Admin\AdminRegistry;
use Perform\BaseBundle\DataCollector\AdminsDataCollector;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Perform\BaseBundle\Admin\AdminInterface;
use Perform\BaseBundle\Action\ActionRegistry;

/**
 * AdminsDataCollectorTest.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class AdminsDataCollectorTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->container = $this->getMock(ContainerInterface::class);
        $this->typeRegistry = new TypeRegistry($this->container);
        $actionRegistry = $this->getMockBuilder(ActionRegistry::class)
                      ->disableOriginalConstructor()
                      ->getMock();
        $this->registry = new AdminRegistry($this->container, $this->typeRegistry, $actionRegistry);
        $this->collector = new AdminsDataCollector($this->registry, []);
    }

    public function testCollectGetsLoadedAdmins()
    {
        $this->container->expects($this->any())
            ->method('get')
            ->will($this->returnValue($this->getMock(AdminInterface::class)));

        $this->registry->addAdmin('FooBundle:Foo', 'FooBundle\\Entity\\Foo', 'foo_service');
        $this->registry->addAdmin('FooBundle:Bar', 'FooBundle\\Entity\\Bar', 'bar_service');
        $this->collector->collect(new Request(), new Response());
        $sortedClasses = ['FooBundle\\Entity\\Bar', 'FooBundle\\Entity\\Foo'];
        $this->assertEquals($sortedClasses, array_keys($this->collector->getAdmins()));
    }
}
