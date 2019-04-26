<?php

namespace MediaBundle\Tests\Url;

use PHPUnit\Framework\TestCase;
use Perform\MediaBundle\Url\SimpleUrlGenerator;
use Perform\MediaBundle\Entity\Location;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class SimpleUrlGeneratorTest extends TestCase
{
    protected $generator;

    public function setUp()
    {
        $this->generator = new SimpleUrlGenerator('http://example.com/uploads');
    }

    public function testGenerate()
    {
        $location = Location::file('foo.jpg');
        $this->assertSame('http://example.com/uploads/foo.jpg', $this->generator->generate($location));
    }
}
