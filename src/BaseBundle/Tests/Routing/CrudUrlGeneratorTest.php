<?php

namespace Perform\BaseBundle\Tests\Routing;

use Perform\BaseBundle\Crud\CrudInterface;
use Perform\BaseBundle\Crud\CrudNotFoundException;
use Perform\BaseBundle\Crud\CrudRegistry;
use Perform\BaseBundle\Routing\CrudUrlGenerator;
use Perform\BaseBundle\Tests\Crud\TestCrud;
use Perform\BaseBundle\Tests\Crud\TestEntity;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\RouterInterface;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class CrudUrlGeneratorTest extends \PHPUnit_Framework_TestCase
{
    protected $crudRegistry;
    protected $router;
    protected $routeCollection;
    protected $generator;

    public function setUp()
    {
        $this->crudRegistry = $this->getMockBuilder(CrudRegistry::class)
                             ->disableOriginalConstructor()
                             ->getMock();
        $this->router = $this->getMock(RouterInterface::class);
        $this->routeCollection = new RouteCollection();
        $this->router->expects($this->any())
            ->method('getRouteCollection')
            ->with()
            ->will($this->returnValue($this->routeCollection));
        $this->generator = new CrudUrlGenerator($this->crudRegistry, $this->router);
    }

    public function testGenerateList()
    {
        $this->crudRegistry->expects($this->any())
            ->method('get')
            ->with('test_crud')
            ->will($this->returnValue(new TestCrud()));
        $this->router->expects($this->any())
            ->method('generate')
            ->with('test_crud_list')
            ->will($this->returnValue('/admin/test'));

        $this->assertSame('/admin/test', $this->generator->generate('test_crud', 'list'));
    }

    public function testGenerateCreate()
    {
        $this->crudRegistry->expects($this->any())
            ->method('get')
            ->with('test_crud')
            ->will($this->returnValue(new TestCrud()));
        $this->router->expects($this->any())
            ->method('generate')
            ->with('test_crud_create')
            ->will($this->returnValue('/admin/test/create'));

        $this->assertSame('/admin/test/create', $this->generator->generate('test_crud', 'create'));
    }

    public function testGenerateView()
    {
        $entity = new TestEntity(1);
        $this->crudRegistry->expects($this->any())
            ->method('get')
            ->with('test_crud')
            ->will($this->returnValue(new TestCrud()));
        $this->router->expects($this->any())
            ->method('generate')
            ->with('test_crud_view', ['id' => 1])
            ->will($this->returnValue('/admin/test/view/1'));

        $this->assertSame('/admin/test/view/1', $this->generator->generate('test_crud', 'view', ['entity' => $entity]));
    }

    public function testGenerateEdit()
    {
        $entity = new TestEntity(1);
        $this->crudRegistry->expects($this->any())
            ->method('get')
            ->with('test_crud')
            ->will($this->returnValue(new TestCrud()));
        $this->router->expects($this->any())
            ->method('generate')
            ->with('test_crud_edit', ['id' => 1])
            ->will($this->returnValue('/admin/test/edit/1'));

        $this->assertSame('/admin/test/edit/1', $this->generator->generate('test_crud', 'edit', ['entity' => $entity]));
    }

    public function testRouteExists()
    {
        $crud = $this->getMock(CrudInterface::class);
        $crud->expects($this->any())
            ->method('getActions')
            ->will($this->returnValue([
                '/' => 'list',
                '/view/{id}' => 'view',
                '/create' => 'create',
                '/edit/{id}' => 'edit',
            ]));
        $crud->expects($this->any())
            ->method('getRoutePrefix')
            ->will($this->returnValue('some_prefix_'));
        $this->routeCollection->add('some_prefix_create', new Route('/'));
        $this->routeCollection->add('some_prefix_modify', new Route('/'));

        $this->crudRegistry->expects($this->any())
            ->method('get')
            ->will($this->returnValue($crud));

        $this->assertTrue($this->generator->routeExists('TestBundle:Something', 'create'));
        $this->assertTrue($this->generator->routeExists(new \stdClass(), 'create'));
        $this->assertFalse($this->generator->routeExists('TestBundle:Something', 'modify'));
        $this->assertFalse($this->generator->routeExists(new \stdClass(), 'modify'));
    }

    public function testRouteExistsButNotLoaded()
    {
        $crud = $this->getMock(CrudInterface::class);
        $crud->expects($this->any())
            ->method('getActions')
            ->will($this->returnValue([
                '/' => 'list',
                '/view/{id}' => 'view',
                '/create' => 'create',
                '/edit/{id}' => 'edit',
            ]));
        $crud->expects($this->any())
            ->method('getRoutePrefix')
            ->will($this->returnValue('some_prefix_'));

        $this->crudRegistry->expects($this->any())
            ->method('get')
            ->will($this->returnValue($crud));

        $this->assertFalse($this->generator->routeExists('TestBundle:Something', 'view'));
        $this->assertFalse($this->generator->routeExists(new \stdClass(), 'view'));
    }

    public function testRouteExistsWithUnknownEntity()
    {
        $this->crudRegistry->expects($this->any())
            ->method('get')
            ->will($this->returnCallback(function() {
                throw new CrudNotFoundException();
            }));

        $this->assertFalse($this->generator->routeExists('Unknown', 'view'));
    }

    public function testGetDefaultEntityRouteList()
    {
        $crud = $this->getMock(CrudInterface::class);
        $crud->expects($this->any())
            ->method('getActions')
            ->will($this->returnValue([
                '/' => 'list',
            ]));
        $crud->expects($this->any())
            ->method('getRoutePrefix')
            ->will($this->returnValue('some_prefix_'));

        $this->crudRegistry->expects($this->any())
            ->method('get')
            ->will($this->returnValue($crud));

        $this->assertSame('some_prefix_list', $this->generator->getDefaultEntityRoute('TestBundle:Something'));
    }

    public function testGetDefaultEntityRouteThrowsExceptionForUnknown()
    {
        $crud = $this->getMock(CrudInterface::class);
        $crud->expects($this->any())
            ->method('getActions')
            ->will($this->returnValue([
                '/{id}' => 'view',
            ]));
        $crud->expects($this->any())
            ->method('getRoutePrefix')
            ->will($this->returnValue('some_prefix_'));

        $this->crudRegistry->expects($this->any())
            ->method('get')
            ->will($this->returnValue($crud));

        $this->setExpectedException(\Exception::class);
        $this->assertSame('some_prefix_view_default', $this->generator->getDefaultEntityRoute('TestBundle:Something'));
    }

    public function testGenerateDefaultEntityRoute()
    {
        $crud = $this->getMock(CrudInterface::class);
        $crud->expects($this->any())
            ->method('getActions')
            ->will($this->returnValue([
                '/' => 'list',
            ]));
        $crud->expects($this->any())
            ->method('getRoutePrefix')
            ->will($this->returnValue('some_prefix_'));

        $this->crudRegistry->expects($this->any())
            ->method('get')
            ->with('test_crud')
            ->will($this->returnValue($crud));

        $this->router->expects($this->any())
            ->method('generate')
            ->with('some_prefix_list')
            ->will($this->returnValue('/some/url'));

        $this->assertSame('/some/url', $this->generator->generateDefaultEntityRoute('test_crud'));
    }
}
