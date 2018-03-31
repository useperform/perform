<?php

namespace Perform\PageEditorBundle\Tests\Entity;

use Perform\PageEditorBundle\Entity\Version;
use Perform\PageEditorBundle\Entity\Section;

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
}
