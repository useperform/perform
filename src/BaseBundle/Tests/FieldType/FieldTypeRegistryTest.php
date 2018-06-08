<?php

namespace Perform\BaseBundle\Tests\Type;

use Perform\BaseBundle\FieldType\FieldTypeInterface;
use Perform\BaseBundle\Exception\TypeNotFoundException;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Perform\BaseBundle\Test\Services;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class TypeRegistryTest extends \PHPUnit_Framework_TestCase
{
    protected $registry;

    protected function register(array $services)
    {
        $this->registry = Services::typeRegistry($services);
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
