<?php

namespace Perform\MediaBundle\Tests\Entity;

use Perform\MediaBundle\Entity\Location;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class LocationTest extends \PHPUnit_Framework_TestCase
{
    public function testFile()
    {
        $location = new Location('/foo/bar/baz.jpg', Location::TYPE_FILE);
        $this->assertSame('/foo/bar/baz.jpg', $location->getPath());
        $this->assertSame(Location::TYPE_FILE, $location->getType());
        $this->assertTrue($location->isFile());
        $this->assertFalse($location->isUrl());
    }

    public function testFileFromStatic()
    {
        $location = Location::file('/foo/bar/baz.jpg');
        $this->assertSame('/foo/bar/baz.jpg', $location->getPath());
        $this->assertSame(Location::TYPE_FILE, $location->getType());
        $this->assertTrue($location->isFile());
        $this->assertFalse($location->isUrl());
    }

    public function testUrl()
    {
        $location = new Location('https://example.com', Location::TYPE_URL);
        $this->assertSame('https://example.com', $location->getPath());
        $this->assertSame(Location::TYPE_URL, $location->getType());
        $this->assertTrue($location->isUrl());
        $this->assertFalse($location->isFile());
    }

    public function testUrlFromStatic()
    {
        $location = Location::url('https://example.com');
        $this->assertSame('https://example.com', $location->getPath());
        $this->assertSame(Location::TYPE_URL, $location->getType());
        $this->assertTrue($location->isUrl());
        $this->assertFalse($location->isFile());
    }
}
