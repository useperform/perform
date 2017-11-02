<?php

namespace Perform\BaseBundle\Tests\DataCollector;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Perform\BaseBundle\Admin\AdminRegistry;
use Perform\BaseBundle\DataCollector\AdminsDataCollector;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Perform\BaseBundle\Admin\AdminInterface;
use Perform\BaseBundle\Config\ConfigStoreInterface;
use Perform\BaseBundle\Doctrine\EntityResolver;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class AdminsDataCollectorTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->container = $this->getMock(ContainerInterface::class);
        $this->registry = new AdminRegistry($this->container, new EntityResolver());
        $store = $this->getMock(ConfigStoreInterface::class);
        $accessManager = $this->getMock(AccessDecisionManagerInterface::class);
        $this->collector = new AdminsDataCollector($this->registry, $store, $accessManager, []);
    }

    public function testCollectGetsLoadedAdmins()
    {
        $this->container->expects($this->any())
            ->method('get')
            ->will($this->returnValue($this->getMock(AdminInterface::class)));

        $this->registry->addAdmin('FooBundle\\Entity\\Foo', 'foo_service');
        $this->registry->addAdmin('FooBundle\\Entity\\Bar', 'bar_service');
        $this->collector->collect(new Request(), new Response());
        $sortedClasses = ['FooBundle\\Entity\\Bar', 'FooBundle\\Entity\\Foo'];
        $this->assertEquals($sortedClasses, array_keys($this->collector->getAdmins()));
    }
}
