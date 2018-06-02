<?php

namespace Perform\BaseBundle\Tests\Config;

use Perform\BaseBundle\Config\ConfigStore;
use Perform\BaseBundle\Crud\CrudInterface;
use Perform\BaseBundle\Doctrine\EntityResolver;
use Perform\BaseBundle\Crud\CrudRegistry;
use Perform\BaseBundle\Type\TypeRegistry;
use Perform\BaseBundle\Config\TypeConfig;
use Perform\BaseBundle\Type\StringType;
use Perform\BaseBundle\Action\ActionRegistry;
use Perform\BaseBundle\Config\FilterConfig;
use Perform\BaseBundle\Config\ActionConfig;
use Perform\BaseBundle\Config\ConfigStoreInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Perform\BaseBundle\Config\ExportConfig;
use Perform\BaseBundle\Test\Services;
use Perform\BaseBundle\Crud\CrudRequest;
use Perform\BaseBundle\Tests\Crud\TestCrud;
use Perform\BaseBundle\Tests\Crud\TestEntity;

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

    private function configure($class, $crud, array $override = [])
    {
        $this->crudRegistry->expects($this->any())
            ->method('get')
            ->with($class)
            ->will($this->returnValue($crud));

        $this->store = new ConfigStore(new EntityResolver([]), $this->crudRegistry, $this->typeRegistry, $this->actionRegistry, $this->authChecker, $override);
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

        $crudName = 'some_crud';
        $this->configure($crudName, $crud);

        $config = $this->store->getTypeConfig($crudName);

        $this->assertInstanceOf(TypeConfig::class, $config);
        //check the same object is always returned
        $this->assertSame($config, $this->store->getTypeConfig($crudName));
    }

    public function testGetActionConfig()
    {
        $crud = $this->getMock(CrudInterface::class);
        $crud->expects($this->once())
            ->method('configureActions')
            ->with($this->callback(function ($config) {
                return $config instanceof ActionConfig;
            }));

        $crudName = 'some_crud';
        $this->configure($crudName, $crud);

        $config = $this->store->getActionConfig($crudName);

        $this->assertInstanceOf(ActionConfig::class, $config);
        //check the same object is always returned
        $this->assertSame($config, $this->store->getActionConfig($crudName));
    }

    public function testGetFilterConfig()
    {
        $crud = $this->getMock(CrudInterface::class);
        $crud->expects($this->once())
            ->method('configureFilters')
            ->with($this->callback(function ($config) {
                return $config instanceof FilterConfig;
            }));

        $crudName = 'some_crud';
        $this->configure($crudName, $crud);

        $config = $this->store->getFilterConfig($crudName);

        $this->assertInstanceOf(FilterConfig::class, $config);
        //check the same object is always returned
        $this->assertSame($config, $this->store->getFilterConfig($crudName));
    }

    public function testGetExportConfig()
    {
        $crud = $this->getMock(CrudInterface::class);
        $crud->expects($this->once())
            ->method('configureExports')
            ->with($this->callback(function ($config) {
                return $config instanceof ExportConfig;
            }));

        $crudName = 'some_crud';
        $this->configure($crudName, $crud);

        $config = $this->store->getExportConfig($crudName);

        $this->assertInstanceOf(ExportConfig::class, $config);
        //check the same object is always returned
        $this->assertSame($config, $this->store->getExportConfig($crudName));
    }

    public function testGetEntityClass()
    {
        $crud = new TestCrud();
        $crudName = 'some_crud';
        $this->configure($crudName, $crud);
        $this->assertSame(TestEntity::class, $this->store->getEntityClass($crudName));
    }
}
