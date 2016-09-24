<?php

namespace Perform\CmsBundle\Tests\Locator;

use Perform\CmsBundle\Locator\SectionLocator;
use Perform\CmsBundle\Entity\Version;
use Perform\CmsBundle\Entity\Section;
use Perform\CmsBundle\Entity\Block;

/**
 * SectionLocatorTest
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class SectionLocatorTest extends \PHPUnit_Framework_TestCase
{
    protected $repo;
    protected $locator;

    public function setUp()
    {
        $this->repo = $this->getMockBuilder('Perform\CmsBundle\Repository\VersionRepository')
                    ->disableOriginalConstructor()
                    ->getMock();
        $this->locator = new SectionLocator($this->repo);
    }

    public function testFindCurrentSections()
    {
        $expected = [];

        $homeVersion = new Version();
        $main = (new Section())->setName('main');
        $block = (new Block())->setType('html')->setValue(['content' => 'main content']);
        $main->addBlock($block);
        $homeVersion->addSection($main);
        $aside = (new Section())->setName('aside');
        $block = (new Block())->setType('html')->setValue(['content' => 'aside content']);
        $aside->addBlock($block);
        $homeVersion->addSection($aside);
        $expected['home'] = [
            'main' => $main->toArray(),
            'aside' => $aside->toArray(),
        ];

        $aboutVersion = new Version();
        $bios = (new Section())->setName('bios');
        $block = (new Block())->setType('html')->setValue(['content' => 'bio 1']);
        $bios->addBlock($block);
        $block = (new Block())->setType('html')->setValue(['content' => 'bio 2']);
        $bios->addBlock($block);
        $aboutVersion->addSection($bios);
        //shouldn't be returned, not in the args
        $other = (new Section())->setName('other_about_section');
        $aboutVersion->addSection($other);
        $expected['about'] = ['bios' => $bios->toArray()];

        $this->repo->expects($this->any())
            ->method('findCurrentVersion')
            ->will($this->returnValueMap([
                ['home', $homeVersion],
                ['about', $aboutVersion],
            ]));

        $sections = $this->locator->findCurrentSections([
            'home' => ['main', 'aside'],
            'about' => ['bios'],
        ]);
        $this->assertSame($expected, $sections);
    }
}
