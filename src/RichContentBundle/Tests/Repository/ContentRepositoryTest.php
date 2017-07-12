<?php

namespace Perform\RichContentBundle\Tests\Repository;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 * @group kernel
 **/
class ContentRepositoryTest extends RepositoryTestCase
{
    protected function createRepo()
    {
        return $this->em->getRepository('PerformRichContentBundle:Content');
    }

    public function testSetBlocks()
    {
        $c = $this->newContent();
        $b1 = $this->newBlock();
        $this->addBlocks($c, [$b1]);
        $b2 = $this->newBlock();
        $b2id = $b2->getId();
        $this->addBlocks($c, [$b2]);

        $b3 = $this->newBlock();
        $this->repo->setBlocks($c, [$b1, $b3]);

        $this->assertSame([$b1, $b3], array_values($c->getBlocks()->toArray()));

        // block 2 should have been removed from the database, since
        // it was only used for that piece of content
        $this->assertNull($this->em->getRepository('PerformRichContentBundle:Block')->find($b2id));
    }
}
