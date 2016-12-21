<?php

namespace Perform\BaseBundle\Tests\Menu\Extension;

use Perform\BaseBundle\Routing\CrudUrlGenerator;
use Perform\BaseBundle\Menu\Extension\EntityExtension;

/**
 * EntityExtensionTest
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class EntityExtensionTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->urlGenerator = $this->getMockBuilder(CrudUrlGenerator::class)
                            ->disableOriginalConstructor()
                            ->getMock();
        $this->extension = new EntityExtension($this->urlGenerator);
    }

    public function testBuildOptionsWithEntityKey()
    {
        $options = [
            'entity' => 'SomeBundle:Entity',
            'route' => 'some_route',
        ];
        $this->urlGenerator->expects($this->any())
            ->method('getDefaultEntityRoute')
            ->with('SomeBundle:Entity')
            ->will($this->returnValue('entity_route_list'));
        $expected = [
            'entity' => 'SomeBundle:Entity',
            //route should be overridden
            'route' => 'entity_route_list',
        ];
        $this->assertSame($expected, $this->extension->buildOptions($options));
    }

    public function testBuildOptionsWithoutEntityKey()
    {
        $options = [
            'route' => 'some_route',
        ];
        $this->urlGenerator->expects($this->never())
            ->method('getDefaultEntityRoute');
        $expected = [
            'route' => 'some_route',
        ];
        $this->assertSame($expected, $this->extension->buildOptions($options));
    }
}
