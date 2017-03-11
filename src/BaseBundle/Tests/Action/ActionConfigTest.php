<?php

namespace Perform\BaseBundle\Tests\Action;

use Perform\BaseBundle\Action\ActionRegistry;
use Perform\BaseBundle\Action\ActionConfig;
use Perform\BaseBundle\Action\ConfiguredAction;
use Perform\BaseBundle\Action\ActionInterface;

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

    public function testAddNoLabels()
    {
        $this->registry->expects($this->any())
            ->method('getAction')
            ->with('foo')
            ->will($this->returnValue($this->getMock(ActionInterface::class)));
        $this->config->add('foo');

        $ca = $this->config->all()['foo'];
        $this->assertInstanceOf(ConfiguredAction::class, $ca);
        $this->assertSame('foo', $ca->getName());
        $this->assertSame('Foo', $ca->getLabel(new \stdClass()));
        $this->assertSame('Foo', $ca->getBatchLabel());
    }

    public function testAddWithStringLabels()
    {
        $this->registry->expects($this->any())
            ->method('getAction')
            ->with('foo')
            ->will($this->returnValue($this->getMock(ActionInterface::class)));
        $this->config->add('foo', [
            'label' => 'Foo label',
            'batchLabel' => 'Foo batch label',
        ]);

        $ca = $this->config->all()['foo'];
        $this->assertInstanceOf(ConfiguredAction::class, $ca);
        $this->assertSame('foo', $ca->getName());
        $this->assertSame('Foo label', $ca->getLabel(new \stdClass()));
        $this->assertSame('Foo batch label', $ca->getBatchLabel());
    }
}
