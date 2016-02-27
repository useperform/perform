<?php

namespace Admin\Base\Tests\Routing;

use Admin\Base\Routing\CrudControllerLoader;

/**
 * CrudControllerLoaderTest
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class CrudControllerLoaderTest extends \PHPUnit_Framework_TestCase
{
    protected $parser;
    protected $loader;

    public function setUp()
    {
        $this->parser = $this->getMockBuilder('Symfony\Bundle\FrameworkBundle\Controller\ControllerNameParser')
                                ->disableOriginalConstructor()
                                ->getMock();
        $this->loader = new CrudControllerLoader($this->parser);
    }

    public function testSupports()
    {
        $this->assertTrue($this->loader->supports('foo', 'crud'));
        $this->assertFalse($this->loader->supports('foo', null));
        $this->assertFalse($this->loader->supports('foo', 'yaml'));
    }

    public function testDefaultRoutes()
    {
        $this->parser->expects($this->any())
            ->method('parse')
            ->with('test:stub:_crud_')
            ->will($this->returnValue('Admin\Base\Tests\Routing\StubController::_crud_'));
        $collection = $this->loader->load('test:stub');
        $this->assertInstanceOf('Symfony\Component\Routing\RouteCollection', $collection);

        $routes = [
            '/' => 'list',
            '/view' => 'view',
            '/create' => 'create',
            '/edit' => 'edit',
            '/delete' => 'delete',
        ];

        foreach ($collection as $route) {
            $this->assertTrue(isset($routes[$route->getPath()]));
            $this->assertSame(
                'Admin\Base\Tests\Routing\StubController::'.$routes[$route->getPath()].'Action',
                $route->getDefault('_controller')
            );
        }
    }
}
