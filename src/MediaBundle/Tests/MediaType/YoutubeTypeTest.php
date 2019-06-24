<?php

namespace Perform\MediaBundle\Tests\MediaType;

use PHPUnit\Framework\TestCase;
use Perform\MediaBundle\MediaType\YoutubeType;
use Perform\MediaBundle\MediaResource;
use Perform\MediaBundle\Event\ImportUrlEvent;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class YoutubeTypeTest extends TestCase
{
    protected $type;

    public function setUp()
    {
        $this->type = new YoutubeType();
    }

    public function urlProvider()
    {
        return [
            ['https://www.youtube.com/watch?v=dQw4w9WgXcQ', 'dQw4w9WgXcQ'],
            ['http://www.youtube.com/watch?v=dQw4w9WgXcQ', 'dQw4w9WgXcQ'],
            ['youtube.com/watch?v=dQw4w9WgXcQ', 'dQw4w9WgXcQ'],
            ['https://youtu.be/dQw4w9WgXcQ', 'dQw4w9WgXcQ'],
            ['youtu.be/dQw4w9WgXcQ', 'dQw4w9WgXcQ'],
        ];
    }

    /**
     * @dataProvider urlProvider
     */
    public function testUrlImportSupportsDifferentRegexSchemes($url, $id)
    {
        $event = new ImportUrlEvent($url);
        $this->type->onUrlImport($event);

        $this->assertSame(1, count($event->getResources()));
        $this->assertSame('youtube:'.$id, $event->getResources()[0]->getPath());
    }

    public function testSupports()
    {
        $id = 'dQw4w9WgXcQ';
        $resource = new MediaResource('youtube:'.$id);

        $this->assertTrue($this->type->supports($resource));
        $this->assertSame($id, $resource->getPath());
    }
}
