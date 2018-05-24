<?php

namespace Perform\BaseBundle\Tests\Routing;

use Perform\BaseBundle\Routing\CrudLoader;
use Symfony\Component\Routing\Route;
use Perform\BaseBundle\Crud\CrudRegistry;
use Perform\BaseBundle\Crud\CrudInterface;

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
        $this->registry = $this->getMockBuilder(CrudRegistry::class)
                                ->disableOriginalConstructor()
                                ->getMock();
        $this->loader = new CrudLoader($this->registry);
    }

    protected function expectCrud($crudName, $controller, $routePrefix, array $actions)
    {
        $crud = $this->getMock(CrudInterface::class);
        $crud->expects($this->any())
            ->method('getControllerName')
            ->will($this->returnValue($controller));
        $crud->expects($this->any())
            ->method('getRoutePrefix')
            ->will($this->returnValue($routePrefix));
        $crud->expects($this->any())
            ->method('getActions')
            ->will($this->returnValue($actions));

        $this->registry->expects($this->any())
            ->method('get')
            ->with($crudName)
            ->will($this->returnValue($crud));
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
        ];
        $crudName = 'some_crud';
        $this->expectCrud($crudName, $controller, $routePrefix, $routes);

        $collection = $this->loader->load($crudName);
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
            '/' => 'someMethod',
            '/edit' => 'someOtherMethod',
        ];
        $crudName = 'another_crud';
        $this->expectCrud($crudName, $controller, $routePrefix, $routes);

        $collection = $this->loader->load($crudName);
        $this->assertInstanceOf('Symfony\Component\Routing\RouteCollection', $collection);

        $this->assertSame(['some_foo_some_method', 'some_foo_some_other_method'], array_keys($collection->all()));

        $route = $collection->get('some_foo_some_method');
        $this->assertInstanceOf(Route::class, $route);
        $this->assertSame(
            $controller.'::'.'someMethodAction',
            $route->getDefault('_controller')
        );

        $route = $collection->get('some_foo_some_other_method');
        $this->assertInstanceOf(Route::class, $route);
        $this->assertSame(
            $controller.'::'.'someOtherMethodAction',
            $route->getDefault('_controller')
        );
    }
}
