<?php

namespace Admin\Base\Tests\Admin;

use Admin\Base\Admin\AdminRegistry;

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
        $admin = $this->getMock('Admin\Base\Admin\AdminInterface');
        $this->registry->addAdmin('Admin\Base\Entity\User', 'admin.service');
        $this->container->expects($this->any())
            ->method('get')
            ->with('admin.service')
            ->will($this->returnValue($admin));

        $this->assertSame($admin, $this->registry->getAdmin('Admin\Base\Entity\User'));
    }

    public function testUnknownAdmin()
    {
        $this->setExpectedException('Admin\Base\Exception\AdminNotFoundException');
        $this->registry->getAdmin('Admin\Base\Entity\Foo');
    }
}
