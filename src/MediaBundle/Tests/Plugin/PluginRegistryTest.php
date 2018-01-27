<?php

namespace MediaBundle\Tests\Plugin;

use Perform\MediaBundle\Exception\MediaTypeException;
use Perform\MediaBundle\MediaType\MediaTypeInterface;
use Perform\MediaBundle\Plugin\PluginRegistry;
use Symfony\Component\DependencyInjection\ServiceLocator;

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
        $plugin = $this->getMock(MediaTypeInterface::class);
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
        $this->setExpectedException(MediaTypeException::class);
        $this->registry->get('foo');
    }
}
