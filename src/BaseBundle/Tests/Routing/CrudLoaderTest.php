<?php

namespace Perform\BaseBundle\Tests\Routing;

use PHPUnit\Framework\TestCase;
use Perform\BaseBundle\Routing\CrudLoader;
use Perform\BaseBundle\Crud\CrudRegistry;
use Perform\BaseBundle\Crud\CrudInterface;
use Perform\BaseBundle\Controller\CrudController;
use Symfony\Component\Routing\Loader\XmlFileLoader;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Config\Loader\LoaderResolver;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class CrudLoaderTest extends TestCase
{
    protected $registry;
    protected $loader;

    public function setUp()
    {
        $this->registry = $this->getMockBuilder(CrudRegistry::class)
                                ->disableOriginalConstructor()
                                ->getMock();
    }

    protected function createLoader(array $options = [])
    {
        return new CrudLoader($this->registry, $options);
    }

    protected function expectCrud($crudName, $controller)
    {
        $crud = $this->createMock(CrudInterface::class);
        $crud->expects($this->any())
            ->method('getControllerName')
            ->will($this->returnValue($controller));

        $this->registry->expects($this->any())
            ->method('get')
            ->with($crudName)
            ->will($this->returnValue($crud));
    }

    public function testSupports()
    {
        $loader = $this->createLoader();
        $this->assertTrue($loader->supports('foo', 'crud'));
        $this->assertFalse($loader->supports('foo', null));
        $this->assertFalse($loader->supports('foo', 'yaml'));
    }

    public function testLoad()
    {
        $crudName = 'some_crud';
        $routePrefix = 'prefix_';
        $contexts = [
            'list' => '/',
            'view' => '/view/{id}',
            'create' => '/create',
            'edit' => '/edit/{id}',
        ];
        $this->expectCrud($crudName, CrudController::class);

        $loader = $this->createLoader([
            'some_crud' => [
                'route_name_prefix' => $routePrefix,
                'contexts' => $contexts,
            ],
        ]);
        $collection = $loader->load($crudName);

        $this->assertInstanceOf(RouteCollection::class, $collection);
        $routes = array_flip($contexts);
        foreach ($collection as $name => $route) {
            $this->assertTrue(isset($routes[$route->getPath()]));
            $this->assertSame(
                CrudController::class.'::'.$routes[$route->getPath()].'Action',
                $route->getDefault('_controller')
            );
            $this->assertStringStartsWith($routePrefix, $name);
        }
    }

    public function testLoadFromXml()
    {
        $crudName = 'xml_crud';
        $routePrefix = 'xml_';
        $contexts = [
            'list' => '/',
            'view' => '/inspect/{id}',
        ];
        $this->expectCrud($crudName, CrudController::class);

        $loader = $this->createLoader([
            $crudName => [
                'route_name_prefix' => $routePrefix,
                'contexts' => $contexts,
            ],
        ]);

        $xmlLoader = new XmlFileLoader(new FileLocator(__DIR__));
        $resolver = new LoaderResolver([$loader]);
        $xmlLoader->setResolver($resolver);

        $collection = $xmlLoader->load('routing.xml');

        $this->assertInstanceOf(RouteCollection::class, $collection);
        $routes = array_flip($contexts);
        foreach ($collection as $name => $route) {
            $this->assertTrue(isset($routes[$route->getPath()]));
            $this->assertSame(
                CrudController::class.'::'.$routes[$route->getPath()].'Action',
                $route->getDefault('_controller')
            );
            $this->assertStringStartsWith($routePrefix, $name);
        }
    }
}
