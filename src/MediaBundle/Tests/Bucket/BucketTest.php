<?php

namespace Perform\MediaBundle\Tests\Bucket;

use Perform\MediaBundle\MediaType\MediaTypeInterface;
use League\Flysystem\FilesystemInterface;
use Perform\MediaBundle\Url\UrlGeneratorInterface;
use Perform\MediaBundle\Bucket\Bucket;
use Perform\MediaBundle\Exception\MediaTypeException;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class BucketTest extends \PHPUnit_Framework_TestCase
{
    protected $flysystem;
    protected $urlGenerator;

    public function setUp()
    {
        $this->flysystem = $this->getMock(FilesystemInterface::class);
        $this->urlGenerator = $this->getMock(UrlGeneratorInterface::class);
    }

    public function testGetMediaType()
    {
        $type = $this->getMock(MediaTypeInterface::class);
        $bucket = new Bucket('test', $this->flysystem, $this->urlGenerator, ['some_type' => $type]);

        $this->assertSame($type, $bucket->getMediaType('some_type'));
    }

    public function testGetUnknownMediaType()
    {
        $bucket = new Bucket('test', $this->flysystem, $this->urlGenerator, []);
        $this->setExpectedException(MediaTypeException::class);
        $bucket->getMediaType('unknown');
    }
}
