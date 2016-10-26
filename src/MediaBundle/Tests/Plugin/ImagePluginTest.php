<?php

namespace Perform\MediaBundle\Tests\Plugin;

use Perform\MediaBundle\Plugin\ImagePlugin;
use Perform\MediaBundle\Entity\File;

/**
 * ImagePluginTest
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class ImagePluginTest extends \PHPUnit_Framework_TestCase
{
    protected $storage;
    protected $urlGenerator;
    protected $imagine;
    protected $plugin;

    public function setUp()
    {
        $this->storage = $this->getMock('League\Flysystem\FilesystemInterface');
        $this->urlGenerator = $this->getMock('Perform\MediaBundle\Url\FileUrlGeneratorInterface');
        $this->imagine = $this->getMock('Imagine\Image\ImagineInterface');
        $this->plugin = new ImagePlugin($this->storage, $this->urlGenerator, $this->imagine);
    }

    public function testProcessCreatesThumbnail()
    {
        $image = $this->getMock('Imagine\Image\ImageInterface');
        $box = $this->getMock('Imagine\Image\BoxInterface');
        $box->expects($this->once())
            ->method('widen')
            ->with(200)
            ->will($this->returnSelf());
        $image->expects($this->any())
            ->method('getSize')
            ->will($this->returnValue($box));
        $image->expects($this->once())
            ->method('resize')
            ->with($box)
            ->will($this->returnSelf());
        $image->expects($this->once())
            ->method('get')
            ->with('jpeg')
            ->will($this->returnValue('thumb_binary'));
        $this->storage->expects($this->once())
            ->method('read')
            ->with('foo.jpg')
            ->will($this->returnValue('image_binary'));
        $this->storage->expects($this->once())
            ->method('write')
            ->with('thumbs/foo.jpg', 'thumb_binary');
        $this->imagine->expects($this->once())
            ->method('load')
            ->with('image_binary')
            ->will($this->returnValue($image));

        $file = new File();
        $file->setType('image');
        $file->setFilename('foo.jpg');
        $this->plugin->onProcess($file);
    }
}
