<?php

namespace Perform\BaseBundle\Tests\Config;

use Perform\BaseBundle\Action\ActionRegistry;
use Perform\BaseBundle\Action\ActionConfig;
use Perform\BaseBundle\Action\ConfiguredAction;
use Perform\BaseBundle\Action\ActionInterface;
use Perform\BaseBundle\Admin\AdminRequest;

/**
 * ActionConfigTest.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class ActionConfigTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->registry = $this->getMockBuilder(ActionRegistry::class)
                        ->disableOriginalConstructor()
                        ->getMock();
        $this->config = new ActionConfig($this->registry);
    }

    protected function stubRequest()
    {
        return $this->getMockBuilder(AdminRequest::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    public function testAddNoLabels()
    {
        $action = $this->getMock(ActionInterface::class);
        $action->expects($this->any())
            ->method('getDefaultConfig')
            ->will($this->returnValue([]));
        $this->registry->expects($this->any())
            ->method('getAction')
            ->with('foo')
            ->will($this->returnValue($action));

        $this->config->add('foo');

        $ca = $this->config->all()['foo'];
        $this->assertInstanceOf(ConfiguredAction::class, $ca);
        $this->assertSame('foo', $ca->getName());
        $this->assertSame('Foo', $ca->getLabel($this->stubRequest(), new \stdClass()));
        $this->assertSame('Foo', $ca->getBatchLabel($this->stubRequest()));
    }

    public function testAddWithStringLabels()
    {
        $action = $this->getMock(ActionInterface::class);
        $action->expects($this->any())
            ->method('getDefaultConfig')
            ->will($this->returnValue([]));
        $this->registry->expects($this->any())
            ->method('getAction')
            ->with('foo')
            ->will($this->returnValue($action));
        $this->config->add('foo', [
            'label' => 'Foo label',
            'batchLabel' => 'Foo batch label',
        ]);

        $ca = $this->config->all()['foo'];
        $this->assertInstanceOf(ConfiguredAction::class, $ca);
        $this->assertSame('foo', $ca->getName());
        $this->assertSame('Foo label', $ca->getLabel($this->stubRequest(), new \stdClass()));
        $this->assertSame('Foo batch label', $ca->getBatchLabel($this->stubRequest()));
    }

    public function testForEntity()
    {
        $entity = new \stdClass();
        $one = $this->getMock(ActionInterface::class);
        $one->expects($this->any())
            ->method('getDefaultConfig')
            ->will($this->returnValue([]));
        $one->expects($this->any())
            ->method('isGranted')
            ->with($entity)
            ->will($this->returnValue(true));
        $two = $this->getMock(ActionInterface::class);
        $two->expects($this->any())
            ->method('getDefaultConfig')
            ->will($this->returnValue([]));
        $two->expects($this->any())
            ->method('isGranted')
            ->with($entity)
            ->will($this->returnValue(false));
        $this->registry->expects($this->any())
            ->method('getAction')
            ->withConsecutive(['foo'], ['bar'])
            ->will($this->onConsecutiveCalls($one, $two));
        $this->config
            ->add('foo')
            ->add('bar');

        $allowed = $this->config->forEntity($entity);
        $this->assertSame(1, count($allowed));
        $this->assertInstanceOf(ConfiguredAction::class, $allowed[0]);
    }
}
