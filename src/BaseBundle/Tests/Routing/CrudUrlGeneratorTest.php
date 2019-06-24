<?php

namespace Perform\BaseBundle\Tests\Routing;

use PHPUnit\Framework\TestCase;
use Perform\BaseBundle\Crud\CrudInterface;
use Perform\BaseBundle\Crud\CrudNotFoundException;
use Perform\BaseBundle\Crud\CrudRegistry;
use Perform\BaseBundle\Routing\CrudUrlGenerator;
use Perform\BaseBundle\Tests\Crud\TestCrud;
use Perform\BaseBundle\Tests\Crud\TestEntity;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Symfony\Component\Routing\Exception\MissingMandatoryParametersException;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class CrudUrlGeneratorTest extends TestCase
{
    protected $urlGenerator;

    public function setUp()
    {
        $this->urlGenerator = $this->createMock(UrlGeneratorInterface::class);
    }

    private function createGenerator(array $routeOptions = [])
    {
        return new CrudUrlGenerator($this->urlGenerator, $routeOptions);
    }

    public function testGenerateList()
    {
        $generator = $this->createGenerator([
            'test_crud' => [
                'route_name_prefix' => 'test_crud_',
            ]
        ]);
        $this->urlGenerator->expects($this->any())
            ->method('generate')
            ->with('test_crud_list')
            ->will($this->returnValue('/admin/test'));

        $this->assertSame('/admin/test', $generator->generate('test_crud', 'list'));
    }

    public function testGenerateCreate()
    {
        $generator = $this->createGenerator([
            'test_crud' => [
                'route_name_prefix' => 'test_crud_',
            ]
        ]);
        $this->urlGenerator->expects($this->any())
            ->method('generate')
            ->with('test_crud_create')
            ->will($this->returnValue('/admin/test/create'));

        $this->assertSame('/admin/test/create', $generator->generate('test_crud', 'create'));
    }

    public function testGenerateView()
    {
        $generator = $this->createGenerator([
            'test_crud' => [
                'route_name_prefix' => 'test_crud_',
            ]
        ]);
        $this->urlGenerator->expects($this->any())
            ->method('generate')
            ->with('test_crud_view', ['id' => 1])
            ->will($this->returnValue('/admin/test/view/1'));
        $entity = new TestEntity(1);

        $this->assertSame('/admin/test/view/1', $generator->generate('test_crud', 'view', ['entity' => $entity]));
    }

    public function testGenerateEdit()
    {
        $generator = $this->createGenerator([
            'test_crud' => [
                'route_name_prefix' => 'test_crud_',
            ]
        ]);
        $this->urlGenerator->expects($this->any())
            ->method('generate')
            ->with('test_crud_edit', ['id' => 1])
            ->will($this->returnValue('/admin/test/edit/1'));
        $entity = new TestEntity(1);

        $this->assertSame('/admin/test/edit/1', $generator->generate('test_crud', 'edit', ['entity' => $entity]));
    }

    public function testRouteExists()
    {
        $generator = $this->createGenerator([
            'test_crud' => [
                'route_name_prefix' => 'test_crud_',
            ]
        ]);
        $this->urlGenerator->expects($this->any())
            ->method('generate')
            ->with('test_crud_create');
        $this->assertTrue($generator->routeExists('test_crud', 'create'));
    }

    public function testRouteExistsHandlesMissingArguments()
    {
        $generator = $this->createGenerator([
            'test_crud' => [
                'route_name_prefix' => 'test_crud_',
            ]
        ]);
        $this->urlGenerator->expects($this->any())
            ->method('generate')
            ->with('test_crud_edit')
            ->will($this->throwException(new MissingMandatoryParametersException('')));
        $this->assertTrue($generator->routeExists('test_crud', 'edit'));
    }

    public function testRouteDoesNotExist()
    {
        $generator = $this->createGenerator([
            'test_crud' => [
                'route_name_prefix' => 'test_crud_',
            ]
        ]);
        $this->urlGenerator->expects($this->any())
            ->method('generate')
            ->with('test_crud_list')
            ->will($this->throwException(new RouteNotFoundException('')));
        $this->assertFalse($generator->routeExists('test_crud', 'list'));
    }

    public function testGetRouteName()
    {
        $generator = $this->createGenerator([
            'test_crud' => [
                'route_name_prefix' => 'test_crud_',
            ]
        ]);
        $this->assertSame('test_crud_list', $generator->getRouteName('test_crud', 'list'));
    }
}
