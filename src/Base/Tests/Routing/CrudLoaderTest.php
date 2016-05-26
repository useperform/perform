<?php

namespace Admin\Base\Tests\Routing;

use Admin\Base\Routing\CrudLoader;

/**
 * CrudLoaderTest
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class CrudLoaderTest extends \PHPUnit_Framework_TestCase
{
    protected $registry;
    protected $loader;

    public function setUp()
    {
        $this->registry = $this->getMockBuilder('Admin\Base\Admin\AdminRegistry')
                                ->disableOriginalConstructor()
                                ->getMock();
        $this->loader = new CrudLoader($this->registry);
    }

    protected function expectAdmin($controller, $routePrefix, array $actions)
    {
        $admin = $this->getMock('Admin\Base\Admin\AdminInterface');
        $admin->expects($this->any())
            ->method('getControllerName')
            ->will($this->returnValue($controller));
        $admin->expects($this->any())
            ->method('getRoutePrefix')
            ->will($this->returnValue($routePrefix));
        $admin->expects($this->any())
            ->method('getActions')
            ->will($this->returnValue($actions));

        $this->registry->expects($this->any())
            ->method('getAdmin')
            ->will($this->returnValue($admin));
    }

    public function testSupports()
    {
        $this->assertTrue($this->loader->supports('foo', 'crud'));
        $this->assertFalse($this->loader->supports('foo', null));
        $this->assertFalse($this->loader->supports('foo', 'yaml'));
    }

    public function testDefaultRoutes()
    {
        $controller = 'Admin\Base\Controller\CrudController';
        $routePrefix = 'some_foo_';
        $routes = [
            '/' => 'list',
            '/view/{id}' => 'view',
            '/create' => 'create',
            '/edit/{id}' => 'edit',
            '/delete/{id}' => 'delete',
        ];
        $this->expectAdmin($controller, $routePrefix, $routes);

        $collection = $this->loader->load('SomeBundle:Foo');
        $this->assertInstanceOf('Symfony\Component\Routing\RouteCollection', $collection);

        foreach ($collection as $name => $route) {
            $this->assertTrue(isset($routes[$route->getPath()]));
            $this->assertSame(
                $controller.'::'.$routes[$route->getPath()].'Action',
                $route->getDefault('_controller')
            );
            $this->assertStringStartsWith($routePrefix, $name);
        }
    }
}
