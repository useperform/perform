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

    public function testProcessCreatesThumbnail()
    {
        $image = $this->getMock(ImageInterface::class);
        $file = new File();
        $this->vfs->createFile('/some_image.jpg', 'Image binary content');
        $resource = new MediaResource($this->vfs->path('/some_image.jpg'));
        $bucket = $this->getMock(BucketInterface::class);

        $this->imagine->expects($this->once())
            ->method('read')
            ->with($this->callback(function ($image) {
                return is_resource($image);
            }))
            ->will($this->returnValue($image));
        $image->expects($this->any())
            ->method('getSize')
            ->will($this->returnValue(new Box(1200, 600)));
        $image->expects($this->once())
            ->method('resize')
            ->with(new Box(200, 100))
            ->will($this->returnSelf());
        $image->expects($this->once())
            ->method('get')
            ->with('jpeg')
            ->will($this->returnValue('thumbnail binary content'));
        $bucket->expects($this->once())
            ->method('save')
            ->with(
                $this->callback(function ($location) {
                    return $location instanceof Location;
                }),
                $this->callback(function ($thumbnail) {
                    return is_resource($thumbnail);
                })
            );

        $type = new ImageType($this->imagine, [200]);
        $type->process($file, $resource, $bucket);
    }

    // public function testGetSuitableLocation()
    // {
    //     $type = new ImageType($this->imagine, [100, 500, 1200]);
    //     $file = new File();
    //     // mock location hasher here
    //     $criteria = ['width' => $desiredWidth];

    //     $location = $type->getSuitableLocation($file, $criteria);
    //     $this->assertSame($expectedWidth, $location->getPath());
    // }
}
