<?php

namespace Admin\CmsBundle\Tests\Entity;

use Admin\CmsBundle\Entity\Section;
use Admin\CmsBundle\Entity\Block;

/**
 * SectionTest
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class SectionTest extends \PHPUnit_Framework_TestCase
{
    public function testAddBlock()
    {
        $section = new Section();
        $this->assertSame(0, count($section->getBlocks()));
        $this->assertSame($section, $section->addBlock($block = new Block()));
        $this->assertSame(1, count($section->getBlocks()));
        $this->assertSame($block, $section->getBlocks()[0]);
        $this->assertSame($section, $block->getSection());
    }

    public function testToArray()
    {
        $section = (new Section())->setName('one');
        $block = (new Block())->setType('html')->setValue(['content' => 'one.one']);
        $section->addBlock($block);
        $block = (new Block())->setType('html')->setValue(['content' => 'one.two']);
        $section->addBlock($block);

        $expected = [
            [
                'type' => 'html',
                'value' => ['content' => 'one.one'],
            ],
            [
                'type' => 'html',
                'value' => ['content' => 'one.two'],
            ],
        ];

        $this->assertSame($expected, $section->toArray());
    }
}
