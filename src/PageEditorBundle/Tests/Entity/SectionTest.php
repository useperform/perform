<?php

namespace Perform\PageEditorBundle\Tests\Entity;

use PHPUnit\Framework\TestCase;
use Perform\PageEditorBundle\Entity\Section;
use Perform\PageEditorBundle\Entity\Version;
use Perform\RichContentBundle\Entity\Content;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class SectionTest extends TestCase
{
    public function testSettersReturnSelf()
    {
        $section = new Section();
        $this->assertSame($section, $section->setName('Untitled'));
        $this->assertSame($section, $section->setVersion(new Version()));
        $this->assertSame($section, $section->setContent(new Content()));
    }
}
