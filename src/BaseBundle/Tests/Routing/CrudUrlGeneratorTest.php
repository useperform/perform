<?php

namespace Perform\BaseBundle\Tests\Routing;

use Perform\BaseBundle\Routing\CrudUrlGenerator;
use Perform\UserBundle\Entity\User;
use Perform\UserBundle\Crud\UserCrud;
use Perform\BaseBundle\Crud\CrudInterface;
use Symfony\Component\Routing\RouterInterface;
use Perform\BaseBundle\Crud\CrudRegistry;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Route;
use Perform\BaseBundle\Crud\CrudNotFoundException;

/**
 * CrudUrlGeneratorTest.
 *
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
        $user = new User();
        $this->crudRegistry->expects($this->any())
            ->method('getCrud')
            ->with($user)
            ->will($this->returnValue(new UserCrud()));
        $this->router->expects($this->any())
            ->method('generate')
            ->with('perform_user_user_list')
            ->will($this->returnValue('/admin/users'));

        $this->assertSame('/admin/users', $this->generator->generate($user, 'list'));
    }

    public function testGenerateListWithString()
    {
        $this->crudRegistry->expects($this->any())
            ->method('getCrud')
            ->with('PerformUserBundle:User')
            ->will($this->returnValue(new UserCrud()));
        $this->router->expects($this->any())
            ->method('generate')
            ->with('perform_user_user_list')
            ->will($this->returnValue('/admin/users'));

        $this->assertSame('/admin/users', $this->generator->generate('PerformUserBundle:User', 'list'));
    }

    public function testGenerateCreateWithString()
    {
        $this->crudRegistry->expects($this->any())
            ->method('getCrud')
            ->with('PerformUserBundle:User')
            ->will($this->returnValue(new UserCrud()));
        $this->router->expects($this->any())
            ->method('generate')
            ->with('perform_user_user_create')
            ->will($this->returnValue('/admin/users/create'));

        $this->assertSame('/admin/users/create', $this->generator->generate('PerformUserBundle:User', 'create'));
    }

    public function testGenerateView()
    {
        $user = $this->getMock('Perform\UserBundle\Entity\User');
        $user->expects($this->any())
            ->method('getId')
            ->will($this->returnValue(1));
        $this->crudRegistry->expects($this->any())
            ->method('getCrud')
            ->with($user)
            ->will($this->returnValue(new UserCrud()));
        $this->router->expects($this->any())
            ->method('generate')
            ->with('perform_user_user_view', ['id' => 1])
            ->will($this->returnValue('/admin/users/view/1'));

        $this->assertSame('/admin/users/view/1', $this->generator->generate($user, 'view'));
    }

    public function testGenerateViewDefault()
    {
        $user = $this->getMock('Perform\UserBundle\Entity\User');
        $user->expects($this->any())
            ->method('getId')
            ->will($this->returnValue(1));
        $this->crudRegistry->expects($this->any())
            ->method('getCrud')
            ->with($user)
            ->will($this->returnValue(new UserCrud()));
        $this->router->expects($this->any())
            ->method('generate')
            ->with('perform_user_user_view_default')
            ->will($this->returnValue('/admin/users'));

        $this->assertSame('/admin/users', $this->generator->generate($user, 'viewDefault'));
        $this->assertSame('/admin/users', $this->generator->generate($user, 'view_default'));
    }

    public function testGenerateEdit()
    {
        $user = $this->getMock('Perform\UserBundle\Entity\User');
        $user->expects($this->any())
            ->method('getId')
            ->will($this->returnValue(1));
        $this->crudRegistry->expects($this->any())
            ->method('getCrud')
            ->with($user)
            ->will($this->returnValue(new UserCrud()));
        $this->router->expects($this->any())
            ->method('generate')
            ->with('perform_user_user_edit', ['id' => 1])
            ->will($this->returnValue('/admin/users/edit/1'));

        $this->assertSame('/admin/users/edit/1', $this->generator->generate($user, 'edit'));
    }

    public function testGenerateEditDefault()
    {
        $user = $this->getMock('Perform\UserBundle\Entity\User');
        $user->expects($this->never())
            ->method('getId');
        $this->crudRegistry->expects($this->any())
            ->method('getCrud')
            ->with($user)
            ->will($this->returnValue(new UserCrud()));
        $this->router->expects($this->any())
            ->method('generate')
            ->with('perform_user_user_edit_default')
            ->will($this->returnValue('/admin/users/edit'));

        $this->assertSame('/admin/users/edit', $this->generator->generate($user, 'editDefault'));
        $this->assertSame('/admin/users/edit', $this->generator->generate($user, 'edit_default'));
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
            ->method('getCrud')
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
            ->method('getCrud')
            ->will($this->returnValue($crud));

        $this->assertFalse($this->generator->routeExists('TestBundle:Something', 'view'));
        $this->assertFalse($this->generator->routeExists(new \stdClass(), 'view'));
    }

    public function testRouteExistsWithUnknownEntity()
    {
        $this->crudRegistry->expects($this->any())
            ->method('getCrud')
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
            ->method('getCrud')
            ->will($this->returnValue($crud));

        $this->assertSame('some_prefix_list', $this->generator->getDefaultEntityRoute('TestBundle:Something'));
        $this->assertSame('some_prefix_list', $this->generator->getDefaultEntityRoute(new User()));
    }

    public function testGetDefaultEntityRouteViewDefault()
    {
        $crud = $this->getMock(CrudInterface::class);
        $crud->expects($this->any())
            ->method('getActions')
            ->will($this->returnValue([
                '/' => 'viewDefault',
            ]));
        $crud->expects($this->any())
            ->method('getRoutePrefix')
            ->will($this->returnValue('some_prefix_'));

        $this->crudRegistry->expects($this->any())
            ->method('getCrud')
            ->will($this->returnValue($crud));

        $this->assertSame('some_prefix_view_default', $this->generator->getDefaultEntityRoute('TestBundle:Something'));
        $this->assertSame('some_prefix_view_default', $this->generator->getDefaultEntityRoute(new User()));
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
            ->method('getCrud')
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
            ->method('getCrud')
            ->will($this->returnValue($crud));

        $this->router->expects($this->any())
            ->method('generate')
            ->with('some_prefix_list')
            ->will($this->returnValue('/some/url'));

        $this->assertSame('/some/url', $this->generator->generateDefaultEntityRoute('TestBundle:Something'));
        $this->assertSame('/some/url', $this->generator->generateDefaultEntityRoute(new User()));
    }
}
