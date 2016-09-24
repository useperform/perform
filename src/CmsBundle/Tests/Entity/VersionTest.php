<?php

namespace Perform\CmsBundle\Tests\Entity;

use Perform\CmsBundle\Entity\Version;
use Perform\CmsBundle\Entity\Section;
use Perform\CmsBundle\Entity\Block;

/**
 * VersionTest.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class VersionTest extends \PHPUnit_Framework_TestCase
{
    public function testAddSection()
    {
        $version = new Version();
        $this->assertSame(0, count($version->getSections()));
        $this->assertSame($version, $version->addSection($section = new Section()));
        $this->assertSame(1, count($version->getSections()));
        $this->assertSame($section, $version->getSections()[0]);
        $this->assertSame($version, $section->getVersion());
    }

    public function testToArray()
    {
        $version = new Version();
        $section = (new Section())->setName('one');
        $block = (new Block())->setType('html')->setValue(['content' => 'one.one']);
        $section->addBlock($block);
        $block = (new Block())->setType('html')->setValue(['content' => 'one.two']);
        $section->addBlock($block);
        $version->addSection($section);

        $section = (new Section())->setName('two');
        $block = (new Block())->setType('html')->setValue(['content' => 'two.one']);
        $section->addBlock($block);
        $block = (new Block())->setType('html')->setValue(['content' => 'two.two']);
        $section->addBlock($block);
        $version->addSection($section);

        $expected = [
            'one' => [
                [
                    'type' => 'html',
                    'value' => ['content' => 'one.one'],
                ],
                [
                    'type' => 'html',
                    'value' => ['content' => 'one.two'],
                ],
            ],
            'two' => [
                [
                    'type' => 'html',
                    'value' => ['content' => 'two.one'],
                ],
                [
                    'type' => 'html',
                    'value' => ['content' => 'two.two'],
                ],
            ],
        ];

        $this->assertSame($expected, $version->toArray());
    }
}
