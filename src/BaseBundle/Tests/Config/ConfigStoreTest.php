<?php

namespace Perform\BaseBundle\Tests\Config;

use Perform\BaseBundle\Config\ConfigStore;
use Perform\BaseBundle\Admin\AdminInterface;
use Perform\BaseBundle\Doctrine\EntityResolver;
use Perform\BaseBundle\Admin\AdminRegistry;
use Perform\BaseBundle\Type\TypeRegistry;
use Perform\BaseBundle\Config\TypeConfig;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Perform\BaseBundle\Type\StringType;
use Perform\BaseBundle\Action\ActionRegistry;
use Perform\BaseBundle\Config\FilterConfig;
use Perform\BaseBundle\Config\ActionConfig;
use Perform\BaseBundle\Config\ConfigStoreInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * ConfigStoreTest.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class ConfigStoreTest extends \PHPUnit_Framework_TestCase
{
    protected $adminRegistry;
    protected $typeRegistry;
    protected $actionRegistry;
    protected $store;
    protected $authChecker;

    public function setUp()
    {
        $this->adminRegistry = $this->getMockBuilder(AdminRegistry::class)
                             ->disableOriginalConstructor()
                             ->getMock();
        $this->typeRegistry = new TypeRegistry($this->getMock(ContainerInterface::class));
        $this->typeRegistry->addType('string', StringType::class);
        $this->actionRegistry = $this->getMockBuilder(ActionRegistry::class)
                      ->disableOriginalConstructor()
                      ->getMock();
        $this->authChecker = $this->getMock(AuthorizationCheckerInterface::class);
    }

    private function configure($alias, $class, $admin, array $override = [])
    {
        $this->adminRegistry->expects($this->any())
            ->method('getAdmin')
            ->with($class)
            ->will($this->returnValue($admin));
        $resolver = new EntityResolver([
            $alias => $class,
        ]);

        $this->store = new ConfigStore($resolver, $this->adminRegistry, $this->typeRegistry, $this->actionRegistry, $this->authChecker, $override);
    }

    public function testInterface()
    {
        $store = new ConfigStore(new EntityResolver(), $this->adminRegistry, $this->typeRegistry, $this->actionRegistry, $this->authChecker);
        $this->assertInstanceOf(ConfigStoreInterface::class, $store);
    }

    public function testGetTypeConfig()
    {
        $admin = $this->getMock(AdminInterface::class);
        $admin->expects($this->once())
            ->method('configureTypes')
            ->with($this->callback(function ($config) {
                return $config instanceof TypeConfig;
            }));

        $alias = 'SomeBundle:stdClass';
        $classname = \stdClass::class;
        $this->configure($alias, $classname, $admin);

        $aliasConfig = $this->store->getTypeConfig($alias);
        $classConfig = $this->store->getTypeConfig($classname);
        $objectConfig = $this->store->getTypeConfig(new \stdClass());

        $this->assertInstanceOf(TypeConfig::class, $aliasConfig);
        //check the same object is always returned
        $this->assertSame($aliasConfig, $classConfig);
        $this->assertSame($aliasConfig, $objectConfig);
        $this->assertSame($aliasConfig, $this->store->getTypeConfig($alias));
    }

    public function testGetTypeConfigWithOverride()
    {
        $override = [
            \stdClass::class => [
                'types' => [
                    'slug' => ['type' => 'string'],
                ],
            ],
        ];
        $admin = $this->getMock(AdminInterface::class);
        $this->configure('SomeBundle:stdClass', \stdClass::class, $admin, $override);

        $config = $this->store->getTypeConfig(\stdClass::class);
        $this->assertArrayHasKey('slug', $config->getTypes(TypeConfig::CONTEXT_LIST));
    }

    public function testGetActionConfig()
    {
        $admin = $this->getMock(AdminInterface::class);
        $admin->expects($this->once())
            ->method('configureActions')
            ->with($this->callback(function($config) {
                    return $config instanceof ActionConfig;
            }));

        $alias = 'SomeBundle:stdClass';
        $classname = \stdClass::class;
        $this->configure($alias, $classname, $admin);

        $aliasConfig = $this->store->getActionConfig($alias);
        $classConfig = $this->store->getActionConfig($classname);
        $objectConfig = $this->store->getActionConfig(new \stdClass());

        $this->assertInstanceOf(ActionConfig::class, $aliasConfig);
        //check the same object is always returned
        $this->assertSame($aliasConfig, $classConfig);
        $this->assertSame($aliasConfig, $objectConfig);
        $this->assertSame($aliasConfig, $this->store->getActionConfig($alias));
    }

    public function testGetFilterConfig()
    {
        $admin = $this->getMock(AdminInterface::class);
        $admin->expects($this->once())
            ->method('configureFilters')
            ->with($this->callback(function($config) {
                    return $config instanceof FilterConfig;
            }));

        $alias = 'SomeBundle:stdClass';
        $classname = \stdClass::class;
        $this->configure($alias, $classname, $admin);

        $aliasConfig = $this->store->getFilterConfig($alias);
        $classConfig = $this->store->getFilterConfig($classname);
        $objectConfig = $this->store->getFilterConfig(new \stdClass());

        $this->assertInstanceOf(FilterConfig::class, $aliasConfig);
        //check the same object is always returned
        $this->assertSame($aliasConfig, $classConfig);
        $this->assertSame($aliasConfig, $objectConfig);
        $this->assertSame($aliasConfig, $this->store->getFilterConfig($alias));
    }
}
