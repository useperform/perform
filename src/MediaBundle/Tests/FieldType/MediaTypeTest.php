<?php

namespace Perform\MediaBundle\Tests\FieldType;

use Perform\BaseBundle\Test\FieldTypeTestCase;
use Perform\MediaBundle\FieldType\MediaType;
use Perform\MediaBundle\Entity\File;
use Perform\MediaBundle\PerformMediaBundle;
use Perform\BaseBundle\Test\TestKernel;
use Symfony\Component\DomCrawler\Crawler;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class MediaTypeTest extends FieldTypeTestCase
{
    protected function createTestKernel()
    {
        return new TestKernel([
            new PerformMediaBundle(),
        ], [__DIR__.'/config.yml']);
    }

    protected function registerTypes()
    {
        return [
            'media' => new MediaType($this->kernel->getContainer()->get('perform_media.bucket_registry')),
        ];
    }

    public function testListContext()
    {
        $obj = new \stdClass();
        $img = new File();
        $img->setType('image');
        $img->setBucketName('test_bucket');
        $obj->image = $img;

        $this->config->add('image', [
            'type' => 'media',
        ]);
        $html = $this->listContext($obj, 'image');
        $crawler = new Crawler();
        $crawler->addHtmlContent($html);

        $this->assertSame('p-comp-media-preview', $crawler->filter('div.p-type-media')->children()->attr('class'));
    }
}
