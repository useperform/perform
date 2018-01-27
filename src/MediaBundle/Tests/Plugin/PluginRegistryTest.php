<?php

namespace MediaBundle\Tests\Plugin;

use Perform\MediaBundle\Plugin\PluginRegistry;
use Symfony\Component\DependencyInjection\ServiceLocator;
use Perform\MediaBundle\Exception\PluginNotFoundException;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class PluginRegistryTest extends \PHPUnit_Framework_TestCase
{
    protected $locator;
    protected $registry;

    public function setUp()
    {
        $this->locator = $this->getMockBuilder(ServiceLocator::class)
                       ->disableOriginalConstructor()
                       ->getMock();
        $this->registry = new PluginRegistry($this->locator);
    }

    public function testAddAndGetPlugin()
    {
        $plugin = $this->getMock(FilePluginInterface::class);
        $this->locator->expects($this->any())
            ->method('has')
            ->with('test')
            ->will($this->returnValue(true));
        $this->locator->expects($this->any())
            ->method('get')
            ->with('test')
            ->will($this->returnValue($plugin));
        $this->assertSame($plugin, $this->registry->get('test'));
    }

    public function testGetUnknown()
    {
        $this->setExpectedException(PluginNotFoundException::class);
        $this->registry->get('foo');
    }
}
