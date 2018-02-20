<?php

namespace Perform\MediaBundle\Tests\File;

use Perform\MediaBundle\File\FinfoParser;
use VirtualFileSystem\FileSystem;
use Perform\MediaBundle\File\ParseResult;

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
        $expected = new ParseResult(
            'text/x-php',
            'us-ascii',
            'php'
        );
        $this->assertEquals($expected, $this->parser->parse(__FILE__));
    }

    public function testParseTextWithBadExtension()
    {
        $this->vfs->createFile('/file.jpg', 'Hello world');
        $expected = new ParseResult(
            'text/plain',
            'us-ascii',
            'txt'
        );
        $this->assertEquals($expected, $this->parser->parse($this->vfs->path('/file.jpg')));
    }

    public function testPlainTextExtensionsArePreserved()
    {
        $this->vfs->createFile('/config.yml', 'foo: bar');
        $expected = new ParseResult(
            'text/plain',
            'us-ascii',
            'yml'
        );
        $this->assertEquals($expected, $this->parser->parse($this->vfs->path('/config.yml')));
    }

    public function testParseImageWithoutExtension()
    {
        $expected = new ParseResult(
            'image/png',
            'binary',
            'png'
        );
        $this->assertEquals($expected, $this->parser->parse(__DIR__.'/../fixtures/image_no_extension'));
    }

    public function testParseBinaryWithoutExtension()
    {
        $expected = new ParseResult(
            'application/octet-stream',
            'binary',
            'bin'
        );
        $this->assertEquals($expected, $this->parser->parse(__DIR__.'/../fixtures/binary_no_extension'));
    }
}
