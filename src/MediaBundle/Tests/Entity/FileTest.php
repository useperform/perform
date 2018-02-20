<?php

namespace Perform\MediaBundle\Tests\Entity;

use Perform\MediaBundle\Entity\File;
use Perform\MediaBundle\Entity\Location;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class FileTest extends \PHPUnit_Framework_TestCase
{
    public function testHasType()
    {
        $file = new File();
        $this->assertFalse($file->hasType());
        $file->setType('pdf');
        $this->assertTrue($file->hasType());
    }

    public function testGetAndSetPrimaryLocation()
    {
        $file = new File();
        $location = Location::file('uh_oh.txt', ['foo' => 'bar']);
        $this->assertSame($file, $file->setPrimaryLocation($location));
        $this->assertEquals($location, $file->getPrimaryLocation());
        $this->assertSame(1, $file->getLocations()->count());
    }

    public function testGetPrimaryLocationHasAFallback()
    {
        $this->assertInstanceOf(Location::class, (new File)->getPrimaryLocation());
    }

    public function testGetNoExtraLocations()
    {
        $file = new File();
        $file->setPrimaryLocation(Location::file('image.jpg', []));
        $this->assertSame(0, $file->getExtraLocations()->count());
    }

    public function testGetExtraLocations()
    {
        $file = new File();
        $file->setPrimaryLocation(Location::file('image.jpg', []));
        $thumb1 = Location::file('thumbnail_1.jpg');
        $thumb2 = Location::file('thumbnail_2.jpg');
        $file->addLocation($thumb1);
        $file->addLocation($thumb2);
        $this->assertSame(2, $file->getExtraLocations()->count());

        // check the collection has ordered array keys
        $this->assertSame($thumb1, $file->getExtraLocations()[0]);
        $this->assertSame($thumb2, $file->getExtraLocations()[1]);
    }
}
