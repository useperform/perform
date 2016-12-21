<?php

namespace Perform\BaseBundle\Tests\Routing;

use Perform\BaseBundle\Routing\CrudLoader;
use Symfony\Component\Routing\Route;

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
        $this->registry = $this->getMockBuilder('Perform\BaseBundle\Admin\AdminRegistry')
                                ->disableOriginalConstructor()
                                ->getMock();
        $this->loader = new CrudLoader($this->registry);
    }

    protected function expectAdmin($controller, $routePrefix, array $actions)
    {
        $admin = $this->getMock('Perform\BaseBundle\Admin\AdminInterface');
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
        $controller = 'Perform\BaseBundle\Controller\CrudController';
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

    public function testRouteNamesGetSlugified()
    {
        $controller = 'Perform\BaseBundle\Controller\CrudController';
        $routePrefix = 'some_foo_';
        $routes = [
            '/' => 'viewDefault',
            '/edit' => 'editDefault',
        ];
        $this->expectAdmin($controller, $routePrefix, $routes);

        $collection = $this->loader->load('SomeBundle:Foo');
        $this->assertInstanceOf('Symfony\Component\Routing\RouteCollection', $collection);

        $this->assertSame(['some_foo_view_default', 'some_foo_edit_default'], array_keys($collection->all()));

        $route = $collection->get('some_foo_view_default');
        $this->assertInstanceOf(Route::class, $route);
        $this->assertSame(
            $controller.'::'.'viewDefaultAction',
            $route->getDefault('_controller')
        );

        $route = $collection->get('some_foo_edit_default');
        $this->assertInstanceOf(Route::class, $route);
        $this->assertSame(
            $controller.'::'.'editDefaultAction',
            $route->getDefault('_controller')
        );
    }
}
