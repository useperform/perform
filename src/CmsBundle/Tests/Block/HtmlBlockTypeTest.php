<?php

namespace Admin\CmsBundle\Tests\Block;

use Admin\CmsBundle\Entity\Block;
use Admin\CmsBundle\Block\HtmlBlockType;

/**
 * HtmlBlockTypeTest
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class HtmlBlockTypeTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->type = new HtmlBlockType();
    }

    public function testRender()
    {
        $block = new Block();
        $html = '<p>Hello, bare html</p>';
        $block->setValue([
            'content' => $html,
        ]);

        $this->assertSame($html, $this->type->render($block));
    }
}
