<?php

namespace BaseBundle\Tests\DependencyInjection\Compiler;

use PHPUnit\Framework\TestCase;
use Perform\BaseBundle\DependencyInjection\Compiler\CrudPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Perform\BaseBundle\Crud\InvalidCrudException;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Perform\BaseBundle\Tests\Crud\TestCrud;
use Perform\BaseBundle\Tests\Crud\OtherTestCrud;
use Perform\BaseBundle\Tests\Crud\InvalidCrud;
use Symfony\Component\DependencyInjection\Reference;
use Perform\BaseBundle\DependencyInjection\LoopableServiceLocator;
use Perform\BaseBundle\Routing\CrudUrlGenerator;
use Perform\BaseBundle\Routing\CrudLoader;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class CrudPassTest extends TestCase
{
    protected $pass;
    protected $container;
    protected $registry;

    public function setUp()
    {
        $this->pass = new CrudPass();
        $this->container = new ContainerBuilder();
        $this->registry = $this->container->register('perform_base.crud.registry', CrudRegistry::class);
        $this->routeLoader = $this->container->register('perform_base.routing.crud_loader', CrudLoader::class);
        $this->routeGenerator = $this->container->register('perform_base.routing.crud_generator', CrudUrlGenerator::class);
    }

    public function testIsCompilerPass()
    {
        $this->assertInstanceOf(CompilerPassInterface::class, $this->pass);
    }

    public function testTaggedServicesAreRegistered()
    {
        $this->container->register('crud.foo', TestCrud::class)
            ->addTag('perform_base.crud', ['crud_name' => 'foo']);
        $this->container->register('crud.bar', OtherTestCrud::class)
            ->addTag('perform_base.crud', ['crud_name' => 'bar']);

        $this->pass->process($this->container);

        $locator = $this->registry->getArgument(2);
        $this->assertSame(LoopableServiceLocator::class, $locator->getClass());
        $expectedFactories = [
            'foo' => new Reference('crud.foo'),
            'bar' => new Reference('crud.bar'),
        ];
        $this->assertEquals($expectedFactories, $locator->getArgument(0));
    }

    public function testCrudWithUnknownEntityThrowsException()
    {
        $this->container->register('crud.invalid', InvalidCrud::class)
            ->addTag('perform_base.crud', ['crud_name' => 'invalid']);

        $this->expectException(InvalidCrudException::class);
        $this->pass->process($this->container);
    }

    public function testServicesAreGivenSensibleNames()
    {
        $this->container->register(TestCrud::class, TestCrud::class)
            ->addTag('perform_base.crud');
        $this->container->register(OtherTestCrud::class, OtherTestCrud::class)
            ->addTag('perform_base.crud');

        $this->pass->process($this->container);

        $locator = $this->registry->getArgument(2);
        $this->assertSame(LoopableServiceLocator::class, $locator->getClass());
        $expectedFactories = [
            'test' => new Reference(TestCrud::class),
            'other_test' => new Reference(OtherTestCrud::class),
        ];
        $this->assertEquals($expectedFactories, $locator->getArgument(0));
    }
}
