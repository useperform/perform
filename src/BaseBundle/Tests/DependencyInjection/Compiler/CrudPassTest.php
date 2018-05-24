<?php

namespace BaseBundle\Tests\DependencyInjection\Compiler;

use Perform\BaseBundle\DependencyInjection\Compiler\CrudPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Perform\BaseBundle\Crud\InvalidCrudException;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Perform\BaseBundle\Tests\Crud\TestCrud;
use Perform\BaseBundle\Tests\Crud\OtherTestCrud;
use Perform\BaseBundle\Tests\Crud\InvalidCrud;
use Symfony\Component\DependencyInjection\Reference;
use Perform\BaseBundle\DependencyInjection\LoopableServiceLocator;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class CrudPassTest extends \PHPUnit_Framework_TestCase
{
    protected $pass;
    protected $container;
    protected $registry;

    public function setUp()
    {
        $this->pass = new CrudPass();
        $this->container = new ContainerBuilder();
        $this->registry = $this->container->register('perform_base.crud.registry', CrudRegistry::class);
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
        $this->container->setParameter('perform_base.entity_aliases', []);
        $this->container->register('crud.invalid', InvalidCrud::class)
            ->addTag('perform_base.crud', ['crud_name' => 'invalid']);

        $this->setExpectedException(InvalidCrudException::class);
        $this->pass->process($this->container);
    }
}
