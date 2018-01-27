<?php

namespace Perform\MediaBundle\Tests\Bucket;

use Perform\MediaBundle\Bucket\BucketRegistry;
use Symfony\Component\DependencyInjection\ServiceLocator;
use Perform\MediaBundle\Entity\File;
use Perform\MediaBundle\Exception\BucketNotFoundException;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class BucketRegistryTest extends \PHPUnit_Framework_TestCase
{
    protected $locator;

    public function setUp()
    {
        $this->locator = $this->getMockBuilder(ServiceLocator::class)
                       ->disableOriginalConstructor()
                       ->getMock();
        $this->registry = new BucketRegistry($this->locator, 'default');
    }

    private function mockBucket($name)
    {
        $bucket = $this->getMock(BucketInterface::class);
        $bucket->expects($this->any())
            ->method('getName')
            ->will($this->returnValue($name));

        $this->locator->expects($this->any())
            ->method('has')
            ->with($name)
            ->will($this->returnValue(true));
        $this->locator->expects($this->any())
            ->method('get')
            ->with($name)
            ->will($this->returnValue($bucket));

        return $bucket;
    }

    public function testGet()
    {
        $bucket = $this->mockBucket('images');
        $this->assertSame($bucket, $this->registry->get('images'));
    }

    public function testGetUnknown()
    {
        $this->setExpectedException(BucketNotFoundException::class);
        $this->registry->get('government_docs');
    }

    public function testGetDefault()
    {
        $bucket = $this->mockBucket('default');
        $this->assertSame($bucket, $this->registry->getDefault());
    }

    public function testGetForFile()
    {
        $file = new File();
        $file->setBucketName('office_docs');
        $bucket = $this->mockBucket('office_docs');

        $this->assertSame($bucket, $this->registry->getForFile($file));
    }
}
