<?php

namespace Perform\BaseBundle\Tests\Type;

use Perform\BaseBundle\Type\TypeRegistry;
use Perform\BaseBundle\Type\TypeInterface;
use Perform\BaseBundle\Exception\TypeNotFoundException;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Perform\BaseBundle\DependencyInjection\LoopableServiceLocator;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class TypeRegistryTest extends \PHPUnit_Framework_TestCase
{
    protected $container;
    protected $registry;

    protected function register(array $services)
    {
        $factories = [];
        foreach ($services as $alias => $service) {
            $factories[$alias] = function () use ($service) { return $service; };
        }
        $locator = new LoopableServiceLocator($factories);
        $this->registry = new TypeRegistry($locator);
    }

    public function testGetType()
    {
        $this->register([
            'one' => $one = $this->getMock(TypeInterface::class),
        ]);

        $this->assertSame($one, $this->registry->getType('one'));
    }

    public function testNotFound()
    {
        $this->register([]);
        $this->setExpectedException(TypeNotFoundException::class);
        $this->registry->getType('foo');
    }

    public function testGetAll()
    {
        $this->register([
            'one' => $one = $this->getMock(TypeInterface::class),
            'two' => $two = $this->getMock(TypeInterface::class),
        ]);

        $this->assertEquals([
            'one' => $one,
            'two' => $two,
        ], $this->registry->getAll());
    }

    public function testGetOptionsResolver()
    {
        $this->register([
            'test' => $type = $this->getMock(TypeInterface::class),
        ]);

        $type->expects($this->once())
            ->method('configureOptions')
            ->with($this->callback(function ($resolver) {
                return $resolver instanceof OptionsResolver;
            }));

        $resolver = $this->registry->getOptionsResolver('test');
        $this->assertInstanceOf(OptionsResolver::class, $resolver);

        //ensure the resolver is reused
        $this->assertSame($resolver, $this->registry->getOptionsResolver('test'));
    }
}
