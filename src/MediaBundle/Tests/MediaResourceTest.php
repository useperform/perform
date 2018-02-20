<?php

namespace Perform\MediaBundle\Tests;

use Perform\MediaBundle\MediaResource;
use VirtualFileSystem\FileSystem;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class MediaResourceTest extends \PHPUnit_Framework_TestCase
{
    protected $vfs;

    public function setUp()
    {
        $this->vfs = new FileSystem();
    }

    public function testDeleteDoesNothingWhenNotAFile()
    {
        $resource = new MediaResource('http://some_url');
        $resource->delete();
    }

    public function testDeleteDoesNothingWithoutBeingMarked()
    {
        $this->vfs->createFile('/file.txt', '');
        $resource = MediaResource::file($this->vfs->path('/file.txt'));
        $resource->delete();
        $this->assertFileExists($this->vfs->path('/file.txt'));
    }

    public function testDeleteWhenMarked()
    {
        $this->vfs->createFile('/file.txt', '');
        $resource = MediaResource::file($this->vfs->path('/file.txt'));
        $resource->deleteAfterProcess();
        $resource->delete();
        $this->assertFileNotExists($this->vfs->path('/file.txt'));
    }

    public function testGetSetPath()
    {
        $resource = new MediaResource('foo.txt');
        $this->assertSame('foo.txt', $resource->getPath());

        $this->assertSame($resource, $resource->setPath('bar.txt'));
        $this->assertSame('bar.txt', $resource->getPath());
    }

}
