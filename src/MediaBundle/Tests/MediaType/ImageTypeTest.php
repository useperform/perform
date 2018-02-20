<?php

namespace Perform\MediaBundle\Tests\MediaType;

use Imagine\Image\Box;
use Imagine\Image\ImageInterface;
use Imagine\Image\ImagineInterface;
use Perform\MediaBundle\Bucket\BucketInterface;
use Perform\MediaBundle\Entity\File;
use Perform\MediaBundle\Entity\Location;
use Perform\MediaBundle\MediaResource;
use Perform\MediaBundle\MediaType\ImageType;
use VirtualFileSystem\FileSystem;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class ImageTypeTest extends \PHPUnit_Framework_TestCase
{
    protected $imagine;
    protected $type;
    protected $vfs;

    public function setUp()
    {
        $this->imagine = $this->getMock(ImagineInterface::class);
        $this->vfs = new FileSystem();
    }

    private function expectRead(ImageInterface $image)
    {
        $this->imagine->expects($this->once())
            ->method('read')
            ->with($this->callback(function ($file) {
                return is_resource($file);
            }))
            ->will($this->returnValue($image));
    }

    private function mockImage($width, $height)
    {
        $image = $this->getMock(ImageInterface::class);
        $image->expects($this->any())
            ->method('getSize')
            ->will($this->returnValue(new Box($width, $height)));

        return $image;
    }

    public function testProcessSetSizeAttributes()
    {
        $file = new File();
        $file->setPrimaryLocation(Location::file('/some_image.jpg'));
        $this->vfs->createFile('/some_image.jpg', 'Image binary content');
        $resource = new MediaResource($this->vfs->path('/some_image.jpg'));
        $bucket = $this->getMock(BucketInterface::class);
        $type = new ImageType($this->imagine);

        $this->expectRead($this->mockImage(1200, 600));
        $type->process($file, $resource, $bucket);
        $this->assertSame(1200, $file->getPrimaryLocation()->getAttribute('width'));
        $this->assertSame(600, $file->getPrimaryLocation()->getAttribute('height'));
    }

    public function testProcessCreatesThumbnail()
    {
        $file = new File();
        $this->vfs->createFile('/some_image.jpg', 'Image binary content');
        $resource = new MediaResource($this->vfs->path('/some_image.jpg'));
        $bucket = $this->getMock(BucketInterface::class);
        $type = new ImageType($this->imagine, [200]);


        $image = $this->mockImage(1200, 600);
        $this->expectRead($image);
        $thumbnail = $this->mockImage(1200, 600);
        $image->expects($this->once())
            ->method('copy')
            ->will($this->returnValue($thumbnail));
        $thumbnail->expects($this->once())
            ->method('resize')
            ->with(new Box(200, 100))
            ->will($this->returnSelf());
        $thumbnail->expects($this->once())
            ->method('get')
            ->with('jpeg')
            ->will($this->returnValue('thumbnail binary content'));
        $bucket->expects($this->once())
            ->method('save')
            ->with(
                $this->callback(function ($location) {
                    return $location instanceof Location;
                }),
                $this->callback(function ($thumbnailFile) {
                    return is_resource($thumbnailFile);
                })
            );

        $type->process($file, $resource, $bucket);
    }

    public function testGetSuitableLocation()
    {
        $type = new ImageType($this->imagine, [100, 500, 1200]);
        $file = new File();
        $file->setPrimaryLocation(Location::file('foo.jpg', ['width' => 2000]));
        $med = Location::file('foo_md.jpg', ['width' => 500]);
        $file->addLocation($med);
        $small = Location::file('foo_sm.jpg', ['width' => 100]);
        $file->addLocation($small);
        $large = Location::file('foo_lg.jpg', ['width' => 1200]);
        $file->addLocation($large);

        $this->assertSame($small, $type->getSuitableLocation($file, ['width' => 50]));
        $this->assertSame($small, $type->getSuitableLocation($file, ['width' => 100]));
        $this->assertSame($med, $type->getSuitableLocation($file, ['width' => 400]));
        $this->assertSame($med, $type->getSuitableLocation($file, ['width' => 500]));
        $this->assertSame($large, $type->getSuitableLocation($file, ['width' => 501]));
        $this->assertSame($large, $type->getSuitableLocation($file, ['width' => 1200]));
        $this->assertSame($file->getPrimaryLocation(), $type->getSuitableLocation($file, ['width' => 1300]));
    }
}
