<?php

namespace Perform\BaseBundle\Tests\Admin;

use Perform\BaseBundle\Admin\AdminRegistry;
use Perform\UserBundle\Entity\User;
use Perform\BaseBundle\Doctrine\EntityResolver;
use Perform\BaseBundle\Admin\AdminInterface;
use Perform\BaseBundle\Exception\AdminNotFoundException;

/**
 * AdminRegistryTest.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class AdminRegistryTest extends \PHPUnit_Framework_TestCase
{
    protected $container;
    protected $registry;

    public function setUp()
    {
        $this->container = $this->getMock('Symfony\Component\DependencyInjection\ContainerInterface');
        $entities = [
            'PerformUserBundle:User' => User::class,
        ];
        $this->registry = new AdminRegistry($this->container, new EntityResolver($entities));
    }

    public function testAddAndGetAdmin()
    {
        $admin = $this->getMock(AdminInterface::class);
        $this->registry->addAdmin(User::class, 'admin.service');
        $this->container->expects($this->any())
            ->method('get')
            ->with('admin.service')
            ->will($this->returnValue($admin));

        $this->assertSame($admin, $this->registry->getAdmin('PerformUserBundle:User'));
    }

    public function testUnknownAdmin()
    {
        $this->setExpectedException(AdminNotFoundException::class);
        $this->registry->getAdmin('PerformBaseBundle:Foo');
    }

    public function testGetAdminInvalidArgument()
    {
        $this->setExpectedException(AdminNotFoundException::class);
        $this->registry->getAdmin(false);
    }

    public function testGetAdminByClass()
    {
        $admin = $this->getMock(AdminInterface::class);
        $this->registry->addAdmin(User::class, 'admin.service');
        $this->container->expects($this->any())
            ->method('get')
            ->with('admin.service')
            ->will($this->returnValue($admin));

        $this->assertSame($admin, $this->registry->getAdmin(User::class));
    }

    public function testGetAdminForEntity()
    {
        $admin = $this->getMock(AdminInterface::class);
        $this->registry->addAdmin(User::class, 'admin.service');
        $this->container->expects($this->any())
            ->method('get')
            ->with('admin.service')
            ->will($this->returnValue($admin));

        $this->assertSame($admin, $this->registry->getAdmin(new User()));
    }

    public function testHasAdmin()
    {
        $this->registry->addAdmin(\stdClass::class, 'admin.service');
        $this->assertTrue($this->registry->hasAdmin(\stdClass::class));
        $this->assertFalse($this->registry->hasAdmin('Perform\\UnknownClass'));
        $this->assertFalse($this->registry->hasAdmin(null));
    }
}
