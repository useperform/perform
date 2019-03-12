<?php

namespace Perform\BaseBundle\Tests\Menu\Extension;

use PHPUnit\Framework\TestCase;
use Perform\BaseBundle\Routing\CrudUrlGenerator;
use Perform\BaseBundle\Menu\Extension\CrudExtension;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class CrudExtensionTest extends TestCase
{
    public function setUp()
    {
        $this->urlGenerator = $this->getMockBuilder(CrudUrlGenerator::class)
                            ->disableOriginalConstructor()
                            ->getMock();
        $this->extension = new CrudExtension($this->urlGenerator);
    }

    public function testBuildOptionsWithCrudKey()
    {
        $options = [
            'crud' => 'some_crud',
            'route' => 'some_route',
        ];
        $this->urlGenerator->expects($this->any())
            ->method('getRouteName')
            ->with('some_crud', 'list')
            ->will($this->returnValue('crud_route_list'));
        $expected = [
            'crud' => 'some_crud',
            //route should be overridden
            'route' => 'crud_route_list',
        ];
        $this->assertSame($expected, $this->extension->buildOptions($options));
    }

    public function testBuildOptionsWithoutCrudKey()
    {
        $options = [
            'route' => 'some_route',
        ];
        $this->urlGenerator->expects($this->never())
            ->method('getRouteName');
        $expected = [
            'route' => 'some_route',
        ];
        $this->assertSame($expected, $this->extension->buildOptions($options));
    }
}
