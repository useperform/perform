<?php

namespace Perform\MediaBundle\Tests\File;

use Perform\MediaBundle\File\FinfoParser;
use VirtualFileSystem\FileSystem;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class FinfoParserTest extends \PHPUnit_Framework_TestCase
{
    protected $parser;
    protected $vfs;

    public function setUp()
    {
        $this->parser = new FinfoParser();
        $this->vfs = new FileSystem();
    }

    public function testParseTextWithExtension()
    {
        $expected = [
            'text/x-php',
            'us-ascii',
            'php',
        ];
        $this->assertSame($expected, $this->parser->parse(__FILE__));
    }

    public function testParseTextWithBadExtension()
    {
        $this->vfs->createFile('/file.jpg', 'Hello world');
        $expected = [
            'text/plain',
            'us-ascii',
            'txt'
        ];
        $this->assertSame($expected, $this->parser->parse($this->vfs->path('/file.jpg')));
    }

    public function testPlainTextExtensionsArePreserved()
    {
        $this->vfs->createFile('/config.yml', 'foo: bar');
        $expected = [
            'text/plain',
            'us-ascii',
            'yml'
        ];
        $this->assertSame($expected, $this->parser->parse($this->vfs->path('/config.yml')));
    }

    public function testParseImageWithoutExtension()
    {
        $expected = [
            'image/png',
            'binary',
            'png',
        ];
        $this->assertSame($expected, $this->parser->parse(__DIR__.'/../fixtures/image_no_extension'));
    }

    public function testParseBinaryWithoutExtension()
    {
        $expected = [
            'application/octet-stream',
            'binary',
            'bin',
        ];
        $this->assertSame($expected, $this->parser->parse(__DIR__.'/../fixtures/binary_no_extension'));
    }
}
