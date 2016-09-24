<?php

namespace Perform\MediaBundle\Tests\Upload;

use Temping\Temping;
use Perform\MediaBundle\Upload\UploadHandler;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * UploadHandlerTest
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class UploadHandlerTest extends \PHPUnit_Framework_TestCase
{
    protected $uh;
    protected $temp;

    public function setUp()
    {
        $this->temp = new Temping();
        $this->uh = new UploadHandler();
    }

    public function tearDown()
    {
        $this->temp->reset();
    }

    public function testChunkPath()
    {
        $this->temp->create('foo.txt');
        $file = new UploadedFile($this->temp->getPathname('foo.txt'), 'foo.txt');
        $expected = $this->temp->getPathname('chunk-'.md5('foo.txt'));
        $this->assertSame($expected, $this->uh->getChunkPath($file));
    }

    protected function newFile($content = null)
    {
        $this->temp->create('foo.txt', $content);

        return new UploadedFile($this->temp->getPathname('foo.txt'), 'foo.txt', null, null, null, true);
    }

    public function testProcessWhole()
    {
        $request = new Request();
        $this->uh->process($request, $this->newFile('foo'));

        $file = new File($this->temp->getPathname('foo.txt'));
        $this->assertSame('foo', file_get_contents($file->getPathname()));

        $this->assertEquals(['foo.txt' => $file], $this->uh->getUploadedFiles());
        $this->assertEquals(['foo.txt' => $file], $this->uh->getCompletedFiles());
        $this->assertSame([], $this->uh->getPartialFiles());
    }

    public function testProcessChunkStart()
    {
        $file = $this->newFile('hello');
        $request = new Request();
        $request->server->set('HTTP_CONTENT_RANGE', 'bytes 0-50000/1000000');
        $this->assertTrue($this->temp->exists('foo.txt'));

        $this->uh->process($request, $file);

        //chunk file should exist
        $chunk = new File($this->uh->getChunkPath($file));
        $this->assertSame('hello', file_get_contents($chunk->getPathname()));
        //original upload should have been deleted
        $this->assertFalse($this->temp->exists('foo.txt'));

        $this->assertEquals(['foo.txt' => $chunk], $this->uh->getUploadedFiles());
        $this->assertSame([], $this->uh->getCompletedFiles());
        $this->assertEquals(['foo.txt' => $chunk], $this->uh->getPartialFiles());
    }

    public function testProcessChunkPartial()
    {
        //upload the start
        $this->testProcessChunkStart();

        //upload the middle
        $file = $this->newFile(' world');
        $request = new Request();
        $request->server->set('HTTP_CONTENT_RANGE', 'bytes 50000-100000/1000000');
        $this->assertTrue($this->temp->exists('foo.txt'));

        $this->uh->process($request, $file);

        //chunk file should exist
        $chunk = new File($this->uh->getChunkPath($file));
        $this->assertSame('hello world', file_get_contents($chunk->getPathname()));
        //original upload should have been deleted
        $this->assertFalse($this->temp->exists('foo.txt'));

        $this->assertEquals(['foo.txt' => $chunk], $this->uh->getUploadedFiles());
        $this->assertSame([], $this->uh->getCompletedFiles());
        $this->assertEquals(['foo.txt' => $chunk], $this->uh->getPartialFiles());
    }

    public function testProcessChunkEnd()
    {
        //upload the start and middle
        $this->testProcessChunkPartial();

        //upload the end
        $file = $this->newFile(', test transmission!');
        $request = new Request();
        $request->server->set('HTTP_CONTENT_RANGE', 'bytes 950000-999999/1000000');
        $this->assertTrue($this->temp->exists('foo.txt'));

        $this->uh->process($request, $file);

        //chunk file should have been renamed to the name of this last upload
        $completed = new File($this->temp->getPathname('foo.txt'));
        $this->assertSame('hello world, test transmission!', file_get_contents($completed->getPathname()));
        $this->assertFileNotExists($this->uh->getChunkPath($file));

        $this->assertEquals(['foo.txt' => $completed], $this->uh->getUploadedFiles());
        $this->assertEquals(['foo.txt' => $completed], $this->uh->getCompletedFiles());
        $this->assertSame([], $this->uh->getPartialFiles());
    }

    public function testProcessInvalidFile()
    {
        $this->temp->create('foo.txt');
        $file = new UploadedFile($this->temp->getPathname('foo.txt'), 'foo.txt', null, null, UPLOAD_ERR_INI_SIZE, true);

        $this->setExpectedException('Symfony\Component\HttpFoundation\File\Exception\UploadException');
        $this->uh->process(new Request(), $file);
    }

}
