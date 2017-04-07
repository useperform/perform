<?php

namespace Perform\BaseBundle\Tests\Type;

use Perform\BaseBundle\Type\TypeRegistry;
use Symfony\Component\DependencyInjection\Container;
use Perform\BaseBundle\Type\TypeInterface;
use Perform\BaseBundle\Type\StringType;
use Perform\BaseBundle\Exception\TypeNotFoundException;

/**
 * TypeRegistryTest.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class TypeRegistryTest extends \PHPUnit_Framework_TestCase
{
    protected $container;
    protected $registry;

    public function setUp()
    {
        $this->container = new Container();
        $this->registry = new TypeRegistry($this->container);
    }

    public function testTypeClass()
    {
        $this->registry->addType('string', StringType::class);

        $one = $this->registry->getType('string');
        $this->assertInstanceOf(StringType::class, $one);

        $two = $this->registry->getType('string');
        $this->assertInstanceOf(StringType::class, $two);

        $this->assertSame($one, $two);
    }

    public function testTypeService()
    {
        $type = $this->getMock(TypeInterface::class);
        $this->container->set('type_service', $type);
        $this->registry->addTypeService('test', 'type_service');

        $this->assertSame($type, $this->registry->getType('test'));
    }

    public function testNotFound()
    {
        $this->setExpectedException(TypeNotFoundException::class);
        $this->registry->getType('foo');
    }

    public function testGetAll()
    {
        $type = $this->getMock(TypeInterface::class);
        $this->container->set('type_service', $type);
        $this->registry->addTypeService('service', 'type_service');

        $this->registry->addType('class', StringType::class);

        $types = $this->registry->getAll();
        $this->assertSame($type, $types['service']);
        $this->assertInstanceOf(StringType::class, $types['class']);
        $this->assertSame(2, count($types));
    }
}
