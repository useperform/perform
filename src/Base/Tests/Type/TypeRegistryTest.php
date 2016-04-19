<?php

namespace Admin\Base\Tests\Type;

use Admin\Base\Type\TypeRegistry;
use Symfony\Component\DependencyInjection\Container;

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
        $this->registry->addType('string', 'Admin\Base\Type\StringType');

        $one = $this->registry->getType('string');
        $this->assertInstanceOf('Admin\Base\Type\StringType', $one);

        $two = $this->registry->getType('string');
        $this->assertInstanceOf('Admin\Base\Type\StringType', $two);

        $this->assertSame($one, $two);
    }

    public function testTypeService()
    {
        $type = $this->getMock('Admin\Base\Type\TypeInterface');
        $this->container->set('type_service', $type);
        $this->registry->addTypeService('test', 'type_service');

        $this->assertSame($type, $this->registry->getType('test'));
    }

    public function testNotFound()
    {
        $this->setExpectedException('Admin\Base\Exception\TypeNotFoundException');
        $this->registry->getType('foo');
    }
}
