<?php

namespace Perform\MediaBundle\Tests\DependencyInjection;

use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\Definition;
use Perform\MediaBundle\MediaType\AudioType;
use Perform\MediaBundle\MediaType\ImageType;
use Perform\MediaBundle\MediaType\OtherType;
use Perform\MediaBundle\MediaType\PdfType;
use Perform\MediaBundle\DependencyInjection\MediaTypeDefinitionFactory;
use Perform\MediaBundle\Exception\MediaTypeException;
use Perform\MediaBundle\MediaType\YoutubeType;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class MediaTypeDefinitionFactoryTest extends TestCase
{
    public function setUp()
    {
        $this->factory = new MediaTypeDefinitionFactory();
    }

    public function testCreateDefaultImage()
    {
        $def = $this->factory->create([
            'type' => 'image',
            'engine' => 'gd',
            'widths' => [],
        ]);

        $this->assertInstanceOf(Definition::class, $def);
        $this->assertSame(ImageType::class, $def->getClass());
    }

    public function testCreateDefaultPdf()
    {
        $def = $this->factory->create([
            'type' => 'pdf',
        ]);

        $this->assertInstanceOf(Definition::class, $def);
        $this->assertSame(PdfType::class, $def->getClass());
    }

    public function testCreateDefaultAudio()
    {
        $def = $this->factory->create([
            'type' => 'audio',
        ]);

        $this->assertInstanceOf(Definition::class, $def);
        $this->assertSame(AudioType::class, $def->getClass());
    }

    public function testCreateDefaultOther()
    {
        $def = $this->factory->create([
            'type' => 'other',
        ]);

        $this->assertInstanceOf(Definition::class, $def);
        $this->assertSame(OtherType::class, $def->getClass());
    }

    public function testCreateDefaultYoutube()
    {
        $def = $this->factory->create([
            'type' => 'youtube',
        ]);

        $this->assertInstanceOf(Definition::class, $def);
        $this->assertSame(YoutubeType::class, $def->getClass());
        $this->assertSame(['kernel.event_listener'], array_keys($def->getTags()));
    }

    public function testUnknownType()
    {
        $this->expectException(MediaTypeException::class);
        $this->factory->create([
            'type' => 'unknown',
        ]);
    }
}
