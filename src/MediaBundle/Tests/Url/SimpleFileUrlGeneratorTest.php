<?php

namespace MediaBundle\Tests\Url;

use Admin\MediaBundle\Url\SimpleFileUrlGenerator;

/**
 * SimpleFileUrlGeneratorTest
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class SimpleFileUrlGeneratorTest extends \PHPUnit_Framework_TestCase
{
    protected $generator;

    public function setUp()
    {
        $this->generator = new SimpleFileUrlGenerator('http://example.com/uploads');
    }

    public function testGetRootUrl()
    {
        $this->assertSame('http://example.com/uploads/', $this->generator->getRootUrl());
    }

    public function testGetUrl()
    {
        $this->assertSame('http://example.com/uploads/foo.jpg', $this->generator->getUrl('foo.jpg'));
    }
}
