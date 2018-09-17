<?php

namespace Perform\RichContentBundle\Tests\FieldType;

use Perform\BaseBundle\Test\FieldTypeTestCase;
use Perform\RichContentBundle\FieldType\RichContentType;
use Perform\BaseBundle\Test\TestKernel;
use Perform\RichContentBundle\PerformRichContentBundle;
use Perform\BaseBundle\Test\WhitespaceAssertions;
use Perform\RichContentBundle\Entity\Content;
use Perform\RichContentBundle\Entity\Block;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class RichContentTypeTest extends FieldTypeTestCase
{
    use WhitespaceAssertions;

    protected function createTestKernel()
    {
        return new TestKernel([
            new PerformRichContentBundle(),
        ]);
    }

    public function registerTypes()
    {
        return [
            'rich_content' => new RichContentType(),
        ];
    }

    public function testViewContextNoContent()
    {
        $obj = new \stdClass();
        $obj->body = null;
        $this->config->add('body', [
            'type' => 'rich_content',
        ]);
        $this->assertTrimmedString('', $this->viewContext($obj, 'body'));
    }

    public function testViewContext()
    {
        $obj = new \stdClass();
        $obj->body = new Content();
        $text = new Block();
        $text->setId(1);
        $text->setType('text');
        $text->setValue(['content' => 'Sample content']);
        $obj->body->addBlock($text);
        $this->config->add('body', [
            'type' => 'rich_content',
        ]);
        $this->assertTrimmedString('<p>Sample content</p>', $this->viewContext($obj, 'body'));
    }
}
