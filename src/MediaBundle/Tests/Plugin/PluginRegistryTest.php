<?php

namespace MediaBundle\Tests\Plugin;

use Admin\MediaBundle\Plugin\PluginRegistry;

/**
 * PluginRegistryTest
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class PluginRegistryTest extends \PHPUnit_Framework_TestCase
{
    protected $registry;

    public function setUp()
    {
        $this->registry = new PluginRegistry();
    }

    public function testAddAndGetPlugin()
    {
        $plugin = $this->getMock('Admin\MediaBundle\Plugin\FilePluginInterface');
        $plugin->expects($this->any())
            ->method('getName')
            ->will($this->returnValue('test'));

        $this->registry->addPlugin($plugin);
        $this->assertSame($plugin, $this->registry->getPlugin('test'));
    }

    public function testGetUnknownPlugin()
    {
        $this->setExpectedException('Admin\MediaBundle\Exception\PluginNotFoundException');
        $this->registry->getPlugin('foo');
    }

}
