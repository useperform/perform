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

    public function testGetAndSetLocation()
    {
        $file = new File();
        $location = Location::file('uh_oh.txt', ['foo' => 'bar']);
        $this->assertSame($file, $file->setLocation($location));
        $this->assertEquals($location, $file->getLocation());
    }
}
