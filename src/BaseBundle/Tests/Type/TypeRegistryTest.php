<?php

namespace Perform\BaseBundle\Tests\Type;

use Perform\BaseBundle\Type\TypeRegistry;
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
        $this->registry->addType('string', 'Perform\BaseBundle\Type\StringType');

        $one = $this->registry->getType('string');
        $this->assertInstanceOf('Perform\BaseBundle\Type\StringType', $one);

        $two = $this->registry->getType('string');
        $this->assertInstanceOf('Perform\BaseBundle\Type\StringType', $two);

        $this->assertSame($one, $two);
    }

    public function testTypeService()
    {
        $type = $this->getMock('Perform\BaseBundle\Type\TypeInterface');
        $this->container->set('type_service', $type);
        $this->registry->addTypeService('test', 'type_service');

        $this->assertSame($type, $this->registry->getType('test'));
    }

    public function testNotFound()
    {
        $this->setExpectedException('Perform\BaseBundle\Exception\TypeNotFoundException');
        $this->registry->getType('foo');
    }
}
