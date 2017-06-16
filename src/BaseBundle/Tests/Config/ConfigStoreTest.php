<?php

namespace SomeBundle\Tests\Config;

use Perform\BaseBundle\Config\ConfigStore;
use Perform\BaseBundle\Admin\AdminInterface;
use Perform\BaseBundle\Doctrine\EntityResolver;
use Perform\BaseBundle\Admin\AdminRegistry;
use Perform\BaseBundle\Type\TypeRegistry;
use Perform\BaseBundle\Type\TypeConfig;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Perform\BaseBundle\Type\StringType;

/**
 * ConfigStoreTest.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class ConfigStoreTest extends \PHPUnit_Framework_TestCase
{
    protected $adminRegistry;
    protected $typeRegistry;
    protected $store;

    public function setUp()
    {
        $this->adminRegistry = $this->getMockBuilder(AdminRegistry::class)
                             ->disableOriginalConstructor()
                             ->getMock();
        $this->typeRegistry = new TypeRegistry($this->getMock(ContainerInterface::class));
        $this->typeRegistry->addType('string', StringType::class);
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

        $this->store = new ConfigStore($resolver, $this->adminRegistry, $this->typeRegistry, $override);
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
}
