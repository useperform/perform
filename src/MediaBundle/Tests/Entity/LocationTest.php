<?php

namespace Perform\MediaBundle\Tests\Entity;

use PHPUnit\Framework\TestCase;
use Perform\MediaBundle\Entity\Location;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class LocationTest extends TestCase
{
    // public function testFile()
    // {
    //     $location = new Location('/foo/bar/baz.jpg');
    //     $location->setFile(true);
    //     $this->assertSame('/foo/bar/baz.jpg', $location->getPath());
    //     $this->assertTrue($location->isFile());
    // }

    public function testFileFromStatic()
    {
        $location = Location::file('/foo/bar/baz.jpg');
        $this->assertSame('/foo/bar/baz.jpg', $location->getPath());
        $this->assertTrue($location->isFile());
    }

    public function testNotFile()
    {
        $location = new Location('https://example.com');
        $this->assertSame('https://example.com', $location->getPath());
        $this->assertFalse($location->isFile());
    }
}
