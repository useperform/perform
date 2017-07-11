<?php

namespace Perform\RichContentBundle\Tests\Repository;

use Perform\RichContentBundle\Entity\Content;
use Perform\BaseBundle\Test\TestKernel;
use Doctrine\ORM\Tools\SchemaTool;
use Perform\RichContentBundle\Entity\Block;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 * @group kernel
 **/
class ContentRepositoryTest extends \PHPUnit_Framework_TestCase
{
    protected $kernel;
    protected $em;
    protected $repo;

    protected function setUp()
    {
        $this->kernel = new TestKernel([
            new \Perform\RichContentBundle\PerformRichContentBundle(),
        ]);
        $this->kernel->boot();
        $this->em = $this->kernel->getContainer()->get('doctrine.orm.entity_manager');
        $this->repo = $this->em->getRepository('PerformRichContentBundle:Content');
        $schemaTool = new SchemaTool($this->em);
        $schemaTool->createSchema([
            $this->em->getClassMetadata(Block::class),
            $this->em->getClassMetadata(Content::class),
        ]);
    }

    protected function tearDown()
    {
        $this->kernel->shutdown();
    }

    protected function newContent()
    {
        $c = new Content();
        $c->setTitle('Test content');

        $this->em->persist($c);
        $this->em->flush();

        return $c;
    }

    protected function newBlock()
    {
        $b = new Block();
        $b->setType('something');
        $b->setValue([]);
        $blocks[] = $b;
        $this->em->persist($b);
        $this->em->flush();

        return $b;
    }

    protected function addBlock($content, $block)
    {
        $content->addBlock($block);
        $this->em->persist($content);
        $this->em->flush();
    }

    public function testSetBlocks()
    {
        $c = $this->newContent();
        $b1 = $this->newBlock();
        $this->addBlock($c, $b1);
        $b2 = $this->newBlock();
        $this->addBlock($c, $b2);

        $b3 = $this->newBlock();
        $this->repo->setBlocks($c, [$b1, $b3]);

        $this->assertSame([$b1, $b3], array_values($c->getBlocks()->toArray()));

        // block 2 should have been removed from the database, since
        // it was only used for that piece of content
        // $this->assertNull($this->em->getRepository('PerformRichContentBundle:Block')->find($b2->getId()));
    }
}
