<?php

namespace Perform\BaseBundle\Tests\DataCollector;

use Perform\BaseBundle\Crud\CrudRegistry;
use Perform\BaseBundle\DataCollector\CrudDataCollector;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Perform\BaseBundle\Config\ConfigStoreInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;
use Perform\BaseBundle\Tests\Crud\TestCrud;
use Perform\BaseBundle\Tests\Crud\TestEntity;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class CrudDataCollectorTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->registry = $this->getMockBuilder(CrudRegistry::class)
                        ->disableOriginalConstructor()
                        ->getMock();
        $store = $this->getMock(ConfigStoreInterface::class);
        $accessManager = $this->getMock(AccessDecisionManagerInterface::class);
        $this->collector = new CrudDataCollector($this->registry, $store, $accessManager, []);
    }

    public function testCollectGetsLoadedCrud()
    {
        $this->registry->expects($this->any())
            ->method('all')
            ->will($this->returnValue([
                'test' => new TestCrud(),
            ]));

        $this->collector->collect(new Request(), new Response());
        $expected = [
            'test' => [
                TestCrud::class,
                TestEntity::class,
            ],
        ];
        $this->assertEquals($expected, $this->collector->getCrudNames());
    }
}
