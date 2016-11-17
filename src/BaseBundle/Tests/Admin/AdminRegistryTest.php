<?php

namespace Perform\BaseBundle\Tests\Admin;

use Perform\BaseBundle\Admin\AdminRegistry;
use Perform\BaseBundle\Entity\User;

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
        $admin = $this->getMock('Perform\BaseBundle\Admin\AdminInterface');
        $this->registry->addAdmin('PerformBaseBundle:User', 'Perform\BaseBundle\Entity\User', 'admin.service');
        $this->container->expects($this->any())
            ->method('get')
            ->with('admin.service')
            ->will($this->returnValue($admin));

        $this->assertSame($admin, $this->registry->getAdmin('PerformBaseBundle:User'));
    }

    public function testUnknownAdmin()
    {
        $this->setExpectedException('Perform\BaseBundle\Exception\AdminNotFoundException');
        $this->registry->getAdmin('PerformBaseBundle:Foo');
    }

    public function testGetAdminByClass()
    {
        $admin = $this->getMock('Perform\BaseBundle\Admin\AdminInterface');
        $this->registry->addAdmin('PerformBaseBundle:User', 'Perform\BaseBundle\Entity\User', 'admin.service');
        $this->container->expects($this->any())
            ->method('get')
            ->with('admin.service')
            ->will($this->returnValue($admin));

        $this->assertSame($admin, $this->registry->getAdmin('Perform\BaseBundle\Entity\User'));
    }

    public function testGetAdminForEntity()
    {
        $admin = $this->getMock('Perform\BaseBundle\Admin\AdminInterface');
        $this->registry->addAdmin('PerformBaseBundle:User', 'Perform\BaseBundle\Entity\User', 'admin.service');
        $this->container->expects($this->any())
            ->method('get')
            ->with('admin.service')
            ->will($this->returnValue($admin));

        $this->assertSame($admin, $this->registry->getAdminForEntity(new User()));
    }

    public function testResolveEntityAlias()
    {
        $alias = 'PerformBaseBundle:User';
        $classname = 'Perform\BaseBundle\Entity\User';
        $this->registry->addAdmin($alias, $classname, 'admin.service');

        $this->assertSame($classname, $this->registry->resolveEntityAlias($alias));
        $this->assertSame($classname, $this->registry->resolveEntityAlias($classname));
    }
}
