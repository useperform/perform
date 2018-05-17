<?php

namespace Perform\BaseBundle\Tests\Crud;

use Perform\BaseBundle\Crud\CrudRegistry;
use Perform\UserBundle\Entity\User;
use Perform\BaseBundle\Doctrine\EntityResolver;
use Perform\BaseBundle\Crud\CrudInterface;
use Perform\BaseBundle\Crud\CrudNotFoundException;

/**
 * CrudRegistryTest.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class CrudRegistryTest extends \PHPUnit_Framework_TestCase
{
    protected $container;
    protected $registry;

    public function setUp()
    {
        $this->container = $this->getMock('Symfony\Component\DependencyInjection\ContainerInterface');
        $entities = [
            'PerformUserBundle:User' => User::class,
        ];
        $this->registry = new CrudRegistry($this->container, new EntityResolver($entities));
    }

    public function testAddAndGetCrud()
    {
        $crud = $this->getMock(CrudInterface::class);
        $this->registry->addCrud(User::class, 'crud.service');
        $this->container->expects($this->any())
            ->method('get')
            ->with('crud.service')
            ->will($this->returnValue($crud));

        $this->assertSame($crud, $this->registry->getCrud('PerformUserBundle:User'));
    }

    public function testUnknownCrud()
    {
        $this->setExpectedException(CrudNotFoundException::class);
        $this->registry->getCrud('PerformBaseBundle:Foo');
    }

    public function testGetCrudInvalidArgument()
    {
        $this->setExpectedException(CrudNotFoundException::class);
        $this->registry->getCrud(false);
    }

    public function testGetCrudByClass()
    {
        $crud = $this->getMock(CrudInterface::class);
        $this->registry->addCrud(User::class, 'crud.service');
        $this->container->expects($this->any())
            ->method('get')
            ->with('crud.service')
            ->will($this->returnValue($crud));

        $this->assertSame($crud, $this->registry->getCrud(User::class));
    }

    public function testGetCrudForEntity()
    {
        $crud = $this->getMock(CrudInterface::class);
        $this->registry->addCrud(User::class, 'crud.service');
        $this->container->expects($this->any())
            ->method('get')
            ->with('crud.service')
            ->will($this->returnValue($crud));

        $this->assertSame($crud, $this->registry->getCrud(new User()));
    }

    public function testHasCrud()
    {
        $this->registry->addCrud(\stdClass::class, 'crud.service');
        $this->assertTrue($this->registry->hasCrud(\stdClass::class));
        $this->assertFalse($this->registry->hasCrud('Perform\\UnknownClass'));
        $this->assertFalse($this->registry->hasCrud(null));
    }
}
