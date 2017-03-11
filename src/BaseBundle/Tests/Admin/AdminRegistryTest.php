<?php

namespace Perform\BaseBundle\Tests\Admin;

use Perform\BaseBundle\Admin\AdminRegistry;
use Perform\BaseBundle\Entity\User;
use Perform\BaseBundle\Type\TypeConfig;
use Perform\BaseBundle\Filter\FilterConfig;
use Perform\BaseBundle\Type\TypeRegistry;
use Perform\BaseBundle\Type\StringType;
use Perform\BaseBundle\Action\ActionRegistry;

/**
 * AdminRegistryTest.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class AdminRegistryTest extends \PHPUnit_Framework_TestCase
{
    protected $container;
    protected $actionRegistry;
    protected $registry;

    public function setUp()
    {
        $this->container = $this->getMock('Symfony\Component\DependencyInjection\ContainerInterface');
        $this->actionRegistry = $this->getMockBuilder(ActionRegistry::class)
                      ->disableOriginalConstructor()
                      ->getMock();
        $this->registry = new AdminRegistry($this->container, $this->stubTypeRegistry(), $this->actionRegistry);
    }

    protected function stubTypeRegistry()
    {

        $typeRegistry = $this->getMockBuilder(TypeRegistry::class)
                      ->disableOriginalConstructor()
                      ->getMock();
        $typeRegistry->expects($this->any())
            ->method('getType')
            ->will($this->returnValue(new StringType()));

        return $typeRegistry;
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

        $this->assertSame($admin, $this->registry->getAdmin(new User()));
    }

    public function testResolveEntity()
    {
        $alias = 'PerformBaseBundle:User';
        $classname = 'Perform\BaseBundle\Entity\User';
        $this->registry->addAdmin($alias, $classname, 'admin.service');

        $this->assertSame($classname, $this->registry->resolveEntity($alias));
        $this->assertSame($classname, $this->registry->resolveEntity($classname));
        $this->assertSame($classname, $this->registry->resolveEntity(new User()));
    }

    public function testGetTypeConfig()
    {
        $admin = $this->getMock('Perform\BaseBundle\Admin\AdminInterface');
        $alias = 'PerformBaseBundle:User';
        $classname = 'Perform\BaseBundle\Entity\User';
        $this->registry->addAdmin($alias, $classname, 'admin.service');
        $this->container->expects($this->any())
            ->method('get')
            ->with('admin.service')
            ->will($this->returnValue($admin));
        $admin->expects($this->once())
            ->method('configureTypes')
            ->with($this->callback(function($config) {
                    return $config instanceof TypeConfig;
            }));

        $aliasConfig = $this->registry->getTypeConfig($alias);
        $classConfig = $this->registry->getTypeConfig($classname);
        $objectConfig = $this->registry->getTypeConfig(new User());

        $this->assertInstanceOf(TypeConfig::class, $aliasConfig);
        //check the same object is always returned
        $this->assertSame($aliasConfig, $classConfig);
        $this->assertSame($aliasConfig, $objectConfig);
        $this->assertSame($aliasConfig, $this->registry->getTypeConfig($alias));
    }

    public function testGetTypeConfigWithOverride()
    {
        $this->container = $this->getMock('Symfony\Component\DependencyInjection\ContainerInterface');
        $override = [
            User::class => [
                'types' => [
                    'slug' => ['type' => 'string']
                ]
            ]
        ];
        $registry = new AdminRegistry($this->container, $this->stubTypeRegistry(), $this->actionRegistry, $override);

        $admin = $this->getMock('Perform\BaseBundle\Admin\AdminInterface');
        $registry->addAdmin('PerformBaseBundle:User', 'Perform\BaseBundle\Entity\User', 'admin.service');
        $this->container->expects($this->any())
            ->method('get')
            ->with('admin.service')
            ->will($this->returnValue($admin));

        $config = $registry->getTypeConfig(User::class);
        $this->assertArrayHasKey('slug', $config->getTypes(TypeConfig::CONTEXT_LIST));
    }

    public function testGetTypeConfigWithAliasOverride()
    {
        $this->container = $this->getMock('Symfony\Component\DependencyInjection\ContainerInterface');
        $override = [
            'PerformBaseBundle:User' => [
                'types' => [
                    'slug' => ['type' => 'string']
                ]
            ]
        ];
        $registry = new AdminRegistry($this->container, $this->stubTypeRegistry(), $this->actionRegistry, $override);

        $admin = $this->getMock('Perform\BaseBundle\Admin\AdminInterface');
        $registry->addAdmin('PerformBaseBundle:User', 'Perform\BaseBundle\Entity\User', 'admin.service');
        $this->container->expects($this->any())
            ->method('get')
            ->with('admin.service')
            ->will($this->returnValue($admin));

        $config = $registry->getTypeConfig(User::class);
        $this->assertArrayHasKey('slug', $config->getTypes(TypeConfig::CONTEXT_LIST));
    }

    public function testGetFilterConfig()
    {
        $admin = $this->getMock('Perform\BaseBundle\Admin\AdminInterface');
        $alias = 'PerformBaseBundle:User';
        $classname = 'Perform\BaseBundle\Entity\User';
        $this->registry->addAdmin($alias, $classname, 'admin.service');
        $this->container->expects($this->any())
            ->method('get')
            ->with('admin.service')
            ->will($this->returnValue($admin));
        $admin->expects($this->once())
            ->method('configureFilters')
            ->with($this->callback(function($config) {
                    return $config instanceof FilterConfig;
            }));

        $aliasConfig = $this->registry->getFilterConfig($alias);
        $classConfig = $this->registry->getFilterConfig($classname);
        $objectConfig = $this->registry->getFilterConfig(new User());

        $this->assertInstanceOf(FilterConfig::class, $aliasConfig);
        //check the same object is always returned
        $this->assertSame($aliasConfig, $classConfig);
        $this->assertSame($aliasConfig, $objectConfig);
        $this->assertSame($aliasConfig, $this->registry->getFilterConfig($alias));
    }
}
