<?php

namespace Perform\RichContentBundle\Tests\Persister;

use Doctrine\ORM\EntityManagerInterface;
use Perform\RichContentBundle\Repository\BlockRepository;
use Perform\RichContentBundle\Repository\ContentRepository;
use Perform\RichContentBundle\Persister\Persister;
use Perform\RichContentBundle\Entity\Content;
use Perform\RichContentBundle\Entity\Block;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class PersisterTest extends \PHPUnit_Framework_TestCase
{
    protected $em;
    protected $blockRepo;
    protected $contentRepo;
    protected $persister;

    public function setUp()
    {
        $this->em = $this->getMock(EntityManagerInterface::class);
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

    public function testSaveFromEditor()
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
        $this->em->expects($this->any())
            ->method('transactional')
            ->will($this->returnCallback(function ($transactionClosure) {
                $transactionClosure();
            }));

        $this->persister->saveFromEditor($content, ['current_defs'], ['new_defs'], ['_new_id', 'current1']);
        $this->assertSame(['new1', 'current1'], $content->getBlockOrder());
    }
}
