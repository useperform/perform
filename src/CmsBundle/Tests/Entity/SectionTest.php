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

    public function testAddBlockAtIndex()
    {
        $section = new Section();
        $this->assertSame($section, $section->addBlock($zero = new Block()));
        $this->assertSame($section, $section->addBlock($two = new Block(), 2));
        $this->assertSame($section, $section->addBlock($one = new Block(), 1));
        $this->assertSame($section, $section->addBlock($three = new Block()));

        $this->assertSame(0, $zero->getSortOrder());
        $this->assertSame(1, $one->getSortOrder());
        $this->assertSame(2, $two->getSortOrder());
        $this->assertSame(3, $three->getSortOrder());

        $section->getBlocks()->clear();
        $this->assertSame(0, count($section->getBlocks()));
        $this->assertSame($section, $section->addBlock($null = new Block()));
        $this->assertSame($section, $section->addBlock($zwei = new Block(), 2));
        $this->assertSame($section, $section->addBlock($eins = new Block(), 1));
        $this->assertSame($section, $section->addBlock($drei = new Block()));

        $this->assertSame(0, $null->getSortOrder());
        $this->assertSame(1, $eins->getSortOrder());
        $this->assertSame(2, $zwei->getSortOrder());
        $this->assertSame(3, $drei->getSortOrder());
    }
}
