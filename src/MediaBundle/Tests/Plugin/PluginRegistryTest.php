<?php

namespace MediaBundle\Tests\Plugin;

use Perform\MediaBundle\Plugin\PluginRegistry;
use Perform\MediaBundle\Url\SimpleFileUrlGenerator;
use Perform\MediaBundle\Entity\File;

/**
 * PluginRegistryTest
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class PluginRegistryTest extends \PHPUnit_Framework_TestCase
{
    protected $registry;
    protected $urlGenerator;

    public function setUp()
    {
        $this->urlGenerator = new SimpleFileUrlGenerator('example.com');
        $this->registry = new PluginRegistry($this->urlGenerator);
    }

    public function testAddAndGetPlugin()
    {
        $plugin = $this->getMock('Perform\MediaBundle\Plugin\FilePluginInterface');
        $plugin->expects($this->any())
            ->method('getName')
            ->will($this->returnValue('test'));

        $this->registry->addPlugin($plugin);
        $this->assertSame($plugin, $this->registry->getPlugin('test'));
    }

    public function testGetUnknownPlugin()
    {
        $this->setExpectedException('Perform\MediaBundle\Exception\PluginNotFoundException');
        $this->registry->getPlugin('foo');
    }

    public function testGetUrl()
    {
        $file = new File();
        $file->setFilename('foo.jpg');
        $this->assertSame('example.com/foo.jpg', $this->registry->getUrl($file));
    }
}
