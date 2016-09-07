<?php

namespace Admin\CmsBundle\Tests\Publisher;

use Admin\CmsBundle\Publisher\Publisher;
use Admin\CmsBundle\Entity\Version;
use Admin\CmsBundle\Entity\Block;
use Admin\CmsBundle\Entity\Section;
use Admin\CmsBundle\Block\BlockTypeRegistry;
use Admin\CmsBundle\Block\HtmlBlockType;

/**
 * PublisherTest.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class PublisherTest extends \PHPUnit_Framework_TestCase
{
    protected $entityManager;
    protected $registry;

    public function setUp()
    {
        $this->entityManager = $this->getMock('Doctrine\ORM\EntityManagerInterface');
        $this->registry = new BlockTypeRegistry($this->getMock('\Twig_Environment'));
        $this->registry->addType('html', new HtmlBlockType());
        $this->publisher = new Publisher($this->entityManager, $this->registry);
    }

    public function testCreatePublishedSections()
    {
        $version = new Version();
        $version->setPage('home');

        $mainBlock = new Block();
        $mainBlock->setType('html');
        $mainBlock->setValue(['content' => '<section>Main</section>']);
        $mainSection = new Section();
        $mainSection->setName('main');
        $mainSection->addBlock($mainBlock);
        $version->addSection($mainSection);

        $asideBlock = new Block();
        $asideBlock->setType('html');
        $asideBlock->setValue(['content' => '<aside>Sidebar</aside>']);
        $asideSection = new Section();
        $asideSection->setName('aside');
        $asideSection->addBlock($asideBlock);
        $version->addSection($asideSection);

        $publishedSections = $this->publisher->createPublishedSections($version);
        $this->assertSame(2, count($publishedSections));

        $main = $publishedSections[0];
        $this->assertSame('home', $main->getPage());
        $this->assertSame('main', $main->getName());
        $this->assertSame('<section>Main</section>', $main->getContent());

        $aside = $publishedSections[1];
        $this->assertSame('home', $aside->getPage());
        $this->assertSame('aside', $aside->getName());
        $this->assertSame('<aside>Sidebar</aside>', $aside->getContent());
    }
}
