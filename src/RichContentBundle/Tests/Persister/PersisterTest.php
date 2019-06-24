<?php

namespace Perform\RichContentBundle\Tests\Persister;

use PHPUnit\Framework\TestCase;
use Doctrine\ORM\EntityManagerInterface;
use Perform\RichContentBundle\Repository\BlockRepository;
use Perform\RichContentBundle\Repository\ContentRepository;
use Perform\RichContentBundle\Persister\Persister;
use Perform\RichContentBundle\Entity\Content;
use Perform\RichContentBundle\Entity\Block;
use Perform\RichContentBundle\Persister\UpdateOperation;
use Perform\RichContentBundle\Persister\CreateOperation;
use Perform\RichContentBundle\Persister\OperationResult;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class PersisterTest extends TestCase
{
    protected $em;
    protected $blockRepo;
    protected $contentRepo;
    protected $persister;

    public function setUp()
    {
        $this->em = $this->createMock(EntityManagerInterface::class);
        $this->blockRepo = $this->getMockBuilder(BlockRepository::class)
                         ->disableOriginalConstructor()
                         ->getMock();
        $this->contentRepo = $this->getMockBuilder(ContentRepository::class)
                           ->disableOriginalConstructor()
                           ->getMock();
        $this->persister = new Persister($this->em, $this->contentRepo, $this->blockRepo);
    }

    private function block($id)
    {
        $b = new Block();
        $refl = new \ReflectionObject($b);
        $prop = $refl->getProperty('id');
        $prop->setAccessible(true);
        $prop->setValue($b, $id);

        return $b;
    }

    public function testSaveWithUpdate()
    {
        $content = new Content();
        $currentBlock = $this->block('current1');
        $content->addBlock($currentBlock);

        $newBlock = $this->block('new1');

        $this->blockRepo->expects($this->once())
            ->method('createFromDefinitions')
            ->with(['new_defs'])
            ->will($this->returnValue(['_new_id' => $newBlock]));
        $this->blockRepo->expects($this->once())
            ->method('updateFromDefinitions')
            ->with(['current_defs'])
            ->will($this->returnValue([$currentBlock]));
        $this->contentRepo->expects($this->once())
            ->method('setBlocks')
            ->with($content, [$newBlock, $currentBlock])
            ->will($this->returnCallback(function () use ($content, $newBlock) {
                $content->addBlock($newBlock);
            }));
        $this->em->expects($this->once())
            ->method('persist')
            ->with($content);
        $this->em->expects($this->once())
            ->method('flush');

        $operation = new UpdateOperation($content, ['current_defs'], ['new_defs'], ['_new_id', 'current1']);
        $result = $this->persister->save($operation);
        $this->assertSame(['_new_id' => 'new1'], $result->getNewIds());
        $this->assertSame($content, $result->getContent());
        $this->assertSame(['new1', 'current1'], $content->getBlockOrder());
    }

    public function testSaveWithCreate()
    {
        $b1 = $this->block('new1');
        $b2 = $this->block('new2');

        $this->blockRepo->expects($this->once())
            ->method('createFromDefinitions')
            ->with(['new_defs'])
            ->will($this->returnValue(['_new_1' => $b1, '_new_2' => $b2]));
        $this->blockRepo->expects($this->once())
            ->method('updateFromDefinitions')
            ->with([])
            ->will($this->returnValue([]));
        $this->contentRepo->expects($this->once())
            ->method('setBlocks')
            ->with($this->callback(function ($val) {
                return $val instanceof Content;
            }), [$b1, $b2])
            ->will($this->returnCallback(function ($content, $blocks) {
                foreach ($blocks as $block) {
                    $content->addBlock($block);
                }
            }));
        $this->em->expects($this->once())
            ->method('persist')
            ->with($this->callback(function ($val) {
                return $val instanceof Content;
            }));
        $this->em->expects($this->once())
            ->method('flush');

        $operation = new CreateOperation(['new_defs'], ['_new_1', '_new_2']);
        $result = $this->persister->save($operation);
        $this->assertInstanceOf(Content::class, $result->getContent());
        $this->assertSame(['_new_1' => 'new1', '_new_2' => 'new2'], $result->getNewIds());
    }

    public function testSaveWithNoNewBlocks()
    {
        $content = new Content();
        $this->blockRepo->expects($this->once())
            ->method('createFromDefinitions')
            ->will($this->returnValue([]));
        $this->blockRepo->expects($this->once())
            ->method('updateFromDefinitions')
            ->will($this->returnValue([]));

        $operation = new UpdateOperation($content, [], [], []);
        $result = $this->persister->save($operation);
        $this->assertSame([], $result->getNewIds());
        $this->assertSame($content, $result->getContent());
    }

    public function testSaveMany()
    {
        $content = new Content();
        $op1 = new UpdateOperation($content, [], [], []);
        $op2 = new UpdateOperation($content, [], [], []);
        $op3 = new UpdateOperation($content, [], [], []);
        $this->blockRepo->expects($this->any())
            ->method('createFromDefinitions')
            ->will($this->returnValue([]));
        $this->blockRepo->expects($this->any())
            ->method('updateFromDefinitions')
            ->will($this->returnValue([]));
        $this->em->expects($this->once())
            ->method('beginTransaction');
        $this->em->expects($this->once())
            ->method('commit');


        $results = $this->persister->saveMany([$op1, $op2, $op3]);
        $this->assertSame(3, count($results));
        $this->assertInstanceOf(OperationResult::class, $results[0]);
        $this->assertInstanceOf(OperationResult::class, $results[1]);
        $this->assertInstanceOf(OperationResult::class, $results[2]);
    }
}
