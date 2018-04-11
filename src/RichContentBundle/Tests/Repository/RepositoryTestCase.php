<?php

namespace Perform\RichContentBundle\Tests\Repository;

use Perform\RichContentBundle\Entity\Content;
use Perform\BaseBundle\Test\TestKernel;
use Doctrine\ORM\Tools\SchemaTool;
use Perform\RichContentBundle\Entity\Block;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
abstract class RepositoryTestCase extends \PHPUnit_Framework_TestCase
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
        $this->repo = $this->createRepo();
        $schemaTool = new SchemaTool($this->em);
        $schemaTool->createSchema([
            $this->em->getClassMetadata(Block::class),
            $this->em->getClassMetadata(Content::class),
        ]);
    }

    abstract protected function createRepo();

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

    protected function addBlocks($content, array $blocks)
    {
        foreach ($blocks as $block) {
            $content->addBlock($block);
        }

        $this->em->persist($content);
        $this->em->flush();
    }
}
