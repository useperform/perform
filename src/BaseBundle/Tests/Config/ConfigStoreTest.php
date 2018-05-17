<?php

namespace Perform\BaseBundle\Tests\Config;

use Perform\BaseBundle\Config\ConfigStore;
use Perform\BaseBundle\Crud\CrudInterface;
use Perform\BaseBundle\Doctrine\EntityResolver;
use Perform\BaseBundle\Crud\CrudRegistry;
use Perform\BaseBundle\Type\TypeRegistry;
use Perform\BaseBundle\Config\TypeConfig;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Perform\BaseBundle\Type\StringType;
use Perform\BaseBundle\Action\ActionRegistry;
use Perform\BaseBundle\Config\FilterConfig;
use Perform\BaseBundle\Config\ActionConfig;
use Perform\BaseBundle\Config\ConfigStoreInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Perform\BaseBundle\Config\ExportConfig;
use Perform\BaseBundle\Test\Services;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class ConfigStoreTest extends \PHPUnit_Framework_TestCase
{
    protected $crudRegistry;
    protected $typeRegistry;
    protected $actionRegistry;
    protected $store;
    protected $authChecker;

    public function setUp()
    {
        $this->crudRegistry = $this->getMockBuilder(CrudRegistry::class)
                             ->disableOriginalConstructor()
                             ->getMock();
        $this->typeRegistry = Services::typeRegistry([
            'string' => new StringType(),
        ]);
        $this->actionRegistry = $this->getMockBuilder(ActionRegistry::class)
                      ->disableOriginalConstructor()
                      ->getMock();
        $this->authChecker = $this->getMock(AuthorizationCheckerInterface::class);
    }

    private function configure($alias, $class, $crud, array $override = [])
    {
        $this->crudRegistry->expects($this->any())
            ->method('getCrud')
            ->with($class)
            ->will($this->returnValue($crud));
        $resolver = new EntityResolver([
            $alias => $class,
        ]);

        $this->store = new ConfigStore($resolver, $this->crudRegistry, $this->typeRegistry, $this->actionRegistry, $this->authChecker, $override);
    }

    public function testInterface()
    {
        $store = new ConfigStore(new EntityResolver(), $this->crudRegistry, $this->typeRegistry, $this->actionRegistry, $this->authChecker);
        $this->assertInstanceOf(ConfigStoreInterface::class, $store);
    }

    public function testGetTypeConfig()
    {
        $crud = $this->getMock(CrudInterface::class);
        $crud->expects($this->once())
            ->method('configureTypes')
            ->with($this->callback(function ($config) {
                return $config instanceof TypeConfig;
            }));

        $alias = 'SomeBundle:stdClass';
        $classname = \stdClass::class;
        $this->configure($alias, $classname, $crud);

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
        $crud = $this->getMock(CrudInterface::class);
        $this->configure('SomeBundle:stdClass', \stdClass::class, $crud, $override);

        $config = $this->store->getTypeConfig(\stdClass::class);
        $this->assertArrayHasKey('slug', $config->getTypes(TypeConfig::CONTEXT_LIST));
    }

    public function testGetActionConfig()
    {
        $crud = $this->getMock(CrudInterface::class);
        $crud->expects($this->once())
            ->method('configureActions')
            ->with($this->callback(function($config) {
                    return $config instanceof ActionConfig;
            }));

        $alias = 'SomeBundle:stdClass';
        $classname = \stdClass::class;
        $this->configure($alias, $classname, $crud);

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
        $crud = $this->getMock(CrudInterface::class);
        $crud->expects($this->once())
            ->method('configureFilters')
            ->with($this->callback(function($config) {
                    return $config instanceof FilterConfig;
            }));

        $alias = 'SomeBundle:stdClass';
        $classname = \stdClass::class;
        $this->configure($alias, $classname, $crud);

        $aliasConfig = $this->store->getFilterConfig($alias);
        $classConfig = $this->store->getFilterConfig($classname);
        $objectConfig = $this->store->getFilterConfig(new \stdClass());

        $this->assertInstanceOf(FilterConfig::class, $aliasConfig);
        //check the same object is always returned
        $this->assertSame($aliasConfig, $classConfig);
        $this->assertSame($aliasConfig, $objectConfig);
        $this->assertSame($aliasConfig, $this->store->getFilterConfig($alias));
    }

    public function testGetExportConfig()
    {
        $crud = $this->getMock(CrudInterface::class);
        $crud->expects($this->once())
            ->method('configureExports')
            ->with($this->callback(function($config) {
                    return $config instanceof ExportConfig;
            }));

        $alias = 'SomeBundle:stdClass';
        $classname = \stdClass::class;
        $this->configure($alias, $classname, $crud);

        $aliasConfig = $this->store->getExportConfig($alias);
        $classConfig = $this->store->getExportConfig($classname);
        $objectConfig = $this->store->getExportConfig(new \stdClass());

        $this->assertInstanceOf(ExportConfig::class, $aliasConfig);
        //check the same object is always returned
        $this->assertSame($aliasConfig, $classConfig);
        $this->assertSame($aliasConfig, $objectConfig);
        $this->assertSame($aliasConfig, $this->store->getExportConfig($alias));
    }
}
