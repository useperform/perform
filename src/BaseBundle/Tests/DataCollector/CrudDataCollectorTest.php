<?php

namespace Perform\BaseBundle\Tests\DataCollector;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Perform\BaseBundle\Crud\CrudRegistry;
use Perform\BaseBundle\DataCollector\CrudDataCollector;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Perform\BaseBundle\Crud\CrudInterface;
use Perform\BaseBundle\Config\ConfigStoreInterface;
use Perform\BaseBundle\Doctrine\EntityResolver;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class CrudDataCollectorTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->container = $this->getMock(ContainerInterface::class);
        $this->registry = new CrudRegistry($this->container, new EntityResolver());
        $store = $this->getMock(ConfigStoreInterface::class);
        $accessManager = $this->getMock(AccessDecisionManagerInterface::class);
        $this->collector = new CrudDataCollector($this->registry, $store, $accessManager, []);
    }

    public function testCollectGetsLoadedCrud()
    {
        $this->container->expects($this->any())
            ->method('get')
            ->will($this->returnValue($this->getMock(CrudInterface::class)));

        $this->registry->addCrud('FooBundle\\Entity\\Foo', 'foo_service');
        $this->registry->addCrud('FooBundle\\Entity\\Bar', 'bar_service');
        $this->collector->collect(new Request(), new Response());
        $sortedClasses = ['FooBundle\\Entity\\Bar', 'FooBundle\\Entity\\Foo'];
        $this->assertEquals($sortedClasses, array_keys($this->collector->getCrud()));
    }
}
