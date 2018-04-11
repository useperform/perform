<?php

namespace Perform\PageEditorBundle\Tests\Entity;

use Perform\PageEditorBundle\Entity\Version;
use Perform\PageEditorBundle\Entity\Section;
use Perform\RichContentBundle\Entity\Content;

/**
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

    public function testGetAllContent()
    {
        $version = new Version();
        $version
            ->addSection($s1 = new Section())
            ->addSection($s2 = new Section())
            ->addSection($s3 = new Section());
        $c1 = new Content();
        $c2 = new Content();

        $s1->setContent($c1);
        $s2->setContent($c2);
        $s3->setContent($c1);

        $this->assertSame([$c1, $c2], $version->getAllContent());
    }

    public function testGetAllContentIgnoresNullContent()
    {
        $version = new Version();
        $version
            ->addSection($s1 = new Section())
            ->addSection($s2 = new Section());
        $c1 = new Content();
        $s1->setContent($c1);

        $this->assertSame([$c1], $version->getAllContent());
    }
}
