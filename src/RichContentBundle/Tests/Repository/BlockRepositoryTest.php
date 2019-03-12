<?php

namespace Perform\RichContentBundle\Tests\Repository;

use Perform\RichContentBundle\Entity\Content;
use Perform\BaseBundle\Test\TestKernel;
use Doctrine\ORM\Tools\SchemaTool;
use Perform\RichContentBundle\Entity\Block;
use Doctrine\ORM\EntityNotFoundException;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 * @group kernel
 **/
class BlockRepositoryTest extends RepositoryTestCase
{
    protected function createRepo()
    {
        return $this->em->getRepository('PerformRichContentBundle:Block');
    }

    public function testCreateFromDefinitions()
    {
        $defs = [
            '_id1' => [
                'type' => 'something',
                'value' => [
                    'content' => 'new block 1',
                ],
            ],
            '_id2' => [
                'type' => 'something',
                'value' => [
                    'content' => 'new block 2',
                ],
            ],
        ];
        $blocks = $this->repo->createFromDefinitions($defs);

        $this->assertSame(2, count($blocks));
        $b1 = $blocks['_id1'];
        $this->assertSame('something', $b1->getType());
        $this->assertSame('new block 1', $b1->getValue()['content']);
        $b2 = $blocks['_id2'];
        $this->assertSame('something', $b2->getType());
        $this->assertSame('new block 2', $b2->getValue()['content']);
    }

    public function testUpdateFromDefinitions()
    {
        $blocks = [];
        $blocks[] = $this->newBlock();
        $blocks[] = $this->newBlock();

        $defs = [
            $blocks[0]->getId() => [
                'type' => 'something',
                'value' => [
                    'content' => 'b1 updated',
                ],
            ],
            $blocks[1]->getId() => [
                'type' => 'something_else',
                'value' => [
                    'content' => 'b2 updated',
                ],
            ],
        ];
        $this->repo->updateFromDefinitions($defs);

        $this->assertSame('b1 updated', $blocks[0]->getValue()['content']);
        $this->assertSame('something', $blocks[0]->getType());
        $this->assertSame('b2 updated', $blocks[1]->getValue()['content']);
        $this->assertSame('something_else', $blocks[1]->getType());
    }

    public function testFindByIdsFailsWhenSomeAreMissing()
    {
        $b1 = $this->newBlock();

        $this->expectException(EntityNotFoundException::class);
        $this->repo->findByIds([$b1->getId(), 'not-an-id']);
    }

    public function testGetUsageCount()
    {
        $c1 = $this->newContent();
        $c2 = $this->newContent();
        $b1 = $this->newBlock();
        $b2 = $this->newBlock();
        $b3 = $this->newBlock();
        $b4 = $this->newBlock();

        $this->addBlocks($c1, [$b1, $b2]);
        $this->addBlocks($c2, [$b1, $b3]);

        $this->assertSame(2, $this->repo->getUsageCount($b1));
        $this->assertSame(1, $this->repo->getUsageCount($b2));
        $this->assertSame(1, $this->repo->getUsageCount($b3));
        $this->assertSame(0, $this->repo->getUsageCount($b4));

        $expected = [
            $b1->getId() => ['count' => 2, 'block' => $b1],
            $b2->getId() => ['count' => 1, 'block' => $b2],
            $b3->getId() => ['count' => 1, 'block' => $b3],
            $b4->getId() => ['count' => 0, 'block' => $b4],
        ];
        $this->assertEquals($expected, $this->repo->getUsageCounts([$b1, $b2, $b3, $b4]));
    }

    public function testRemoveUnused()
    {
        $c1 = $this->newContent();
        $b1 = $this->newBlock();
        $b2 = $this->newBlock();
        $b3 = $this->newBlock();
        $b3id = $b3->getId();

        $this->addBlocks($c1, [$b1, $b2]);
        $this->repo->removeIfUnused([$b1, $b2, $b3]);
        $this->assertNull($this->repo->find($b3id));
    }
}
