<?php

namespace Admin\CmsBundle\Tests\Entity;

use Admin\CmsBundle\Entity\Version;
use Admin\CmsBundle\Entity\Section;

/**
 * VersionTest
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
}
