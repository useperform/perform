<?php

namespace Perform\MediaBundle\Tests\Upload;

use PHPUnit\Framework\TestCase;
use Temping\Temping;
use Perform\MediaBundle\Upload\UploadHandler;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Perform\MediaBundle\Upload\UploadResult;
use Symfony\Component\HttpFoundation\File\Exception\UploadException;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class UploadHandlerTest extends TestCase
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
        $result = $this->uh->process($request, $this->newFile('foo'));

        $this->assertSame(UploadResult::WHOLE, $result->getChunkStatus());
        $this->assertSame($this->temp->getPathname('foo.txt'), $result->getFile()->getPathname());
    }

    protected function uploadChunk($content, $header)
    {
        $file = $this->newFile($content);
        $request = new Request();
        $request->server->set('HTTP_CONTENT_RANGE', $header);

        return $this->uh->process($request, $file);
    }

    public function testProcessChunkStart()
    {
        $result = $this->uploadChunk('hello', 'bytes 0-4/10');

        $this->assertSame(UploadResult::CHUNK_START, $result->getChunkStatus());
        $this->assertSame($this->temp->getPathname('chunk-'.md5('foo.txt')), $result->getFile()->getPathname());
        $this->assertSame('hello', file_get_contents($result->getFile()->getPathname()));
        //original upload should have been deleted
        $this->assertFalse($this->temp->exists('foo.txt'));
    }

    public function testProcessChunkPartial()
    {
        //upload the start
        $this->uploadChunk('hello', 'bytes 0-4/3000');
        //upload the middle
        $result = $this->uploadChunk(' world', 'bytes 5-10/3000');

        $this->assertSame(UploadResult::CHUNK_PARTIAL, $result->getChunkStatus());
        $this->assertSame('hello world', file_get_contents($result->getFile()->getPathname()));
        //original upload should have been deleted
        $this->assertFalse($this->temp->exists('foo.txt'));
    }

    public function testProcessChunkEnd()
    {
        //upload the start and middle
        $this->uploadChunk('hello', 'bytes 0-4/40');
        $this->uploadChunk(' world', 'bytes 4-10/40');
        // upload the end
        $result = $this->uploadChunk(', test transmission!', 'bytes 11-30/31');

        $this->assertSame(UploadResult::CHUNK_END, $result->getChunkStatus());
        $this->assertSame('hello world, test transmission!', file_get_contents($result->getFile()->getPathname()));
        //chunk file should have been renamed to the name of this upload
        $this->assertFileNotExists($this->temp->getPathname('chunk-'.md5('foo.txt')));
    }

    public function testProcessBadContentHeader()
    {
        $this->expectException(UploadException::class);
        $this->uploadChunk('hello', 'bytes 0-5/6');
    }

    public function testProcessInvalidFile()
    {
        $this->temp->create('foo.txt');
        $file = new UploadedFile($this->temp->getPathname('foo.txt'), 'foo.txt', null, null, UPLOAD_ERR_INI_SIZE, true);

        $this->expectException(UploadException::class);
        $this->uh->process(new Request(), $file);
    }
}
