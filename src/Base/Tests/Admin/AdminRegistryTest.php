<?php

namespace Perform\Base\Tests\Admin;

use Perform\Base\Admin\AdminRegistry;
use Perform\Base\Entity\User;

/**
 * AdminRegistryTest.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class AdminRegistryTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->container = $this->getMock('Symfony\Component\DependencyInjection\ContainerInterface');
        $this->registry = new AdminRegistry($this->container);
    }

    public function testAddAndGetAdmin()
    {
        $admin = $this->getMock('Perform\Base\Admin\AdminInterface');
        $this->registry->addAdmin('PerformBaseBundle:User', 'Perform\Base\Entity\User', 'admin.service');
        $this->container->expects($this->any())
            ->method('get')
            ->with('admin.service')
            ->will($this->returnValue($admin));

        $this->assertSame($admin, $this->registry->getAdmin('PerformBaseBundle:User'));
    }

    public function testUnknownAdmin()
    {
        $this->setExpectedException('Perform\Base\Exception\AdminNotFoundException');
        $this->registry->getAdmin('PerformBaseBundle:Foo');
    }

    public function testGetAdminByClass()
    {
        $admin = $this->getMock('Perform\Base\Admin\AdminInterface');
        $this->registry->addAdmin('PerformBaseBundle:User', 'Perform\Base\Entity\User', 'admin.service');
        $this->container->expects($this->any())
            ->method('get')
            ->with('admin.service')
            ->will($this->returnValue($admin));

        $this->assertSame($admin, $this->registry->getAdmin('Perform\Base\Entity\User'));
    }

    public function testGetAdminForEntity()
    {
        $admin = $this->getMock('Perform\Base\Admin\AdminInterface');
        $this->registry->addAdmin('PerformBaseBundle:User', 'Perform\Base\Entity\User', 'admin.service');
        $this->container->expects($this->any())
            ->method('get')
            ->with('admin.service')
            ->will($this->returnValue($admin));

        $this->assertSame($admin, $this->registry->getAdminForEntity(new User()));
    }
}
