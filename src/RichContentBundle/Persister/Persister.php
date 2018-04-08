<?php

namespace Perform\RichContentBundle\Persister;

use Doctrine\ORM\EntityManagerInterface;
use Perform\RichContentBundle\Repository\ContentRepository;
use Perform\RichContentBundle\Repository\BlockRepository;
use Perform\RichContentBundle\Entity\Content;

/**
 * Updates content that has been edited, maintaining internal consistency.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class Persister
{
    protected $em;
    protected $contentRepo;
    protected $blockRepo;

    public function __construct(EntityManagerInterface $em, ContentRepository $contentRepo, BlockRepository $blockRepo)
    {
        $this->em = $em;
        $this->contentRepo = $contentRepo;
        $this->blockRepo = $blockRepo;
    }

    /**
     * Create or update content using data from the frontend editor.
     *
     * @return Block[] An array of newly created blocks, indexed by
     * the stub ids that were passed in.
     */
    public function save(OperationInterface $operation)
    {
        $this->em->beginTransaction();
        try {
            $result = $this->doSave($operation);
            $this->em->commit();

            return $result;
        } catch (\Exception $e) {
            $this->em->rollback();
            throw $e;
        }
    }

    public function saveMany(array $operations)
    {
        $this->em->beginTransaction();
        try {
            foreach ($operations as $operation) {
                $this->doSave($operation);
                $this->em->commit();
            }
        } catch (\Exception $e) {
            $this->em->rollback();
            throw $e;
        }
    }

    private function doSave(OperationInterface $operation)
    {
        $newBlocks = $this->blockRepo->createFromDefinitions($operation->getNewBlockDefinitions());
        $newIds = [];
        foreach ($newBlocks as $stubId => $block) {
            $newIds[$stubId] = $block->getId();
        }
        $currentBlocks = $this->blockRepo->updateFromDefinitions($operation->getBlockDefinitions());
        $blocks = array_merge(array_values($newBlocks), $currentBlocks);

        // replace stub ids from new definitions in the block order
        // with newly acquired database ids
        // e.g. _983983498234 => some-guid-238498-230993
        $blockOrder = $operation->getBlockOrder();
        foreach ($blockOrder as $position => $id) {
            if (isset($newBlocks[$id])) {
                $blockOrder[$position] = $newBlocks[$id]->getId();
            }
        }

        $content = $operation->getContent();
        $this->contentRepo->setBlocks($content, $blocks);
        $content->setBlockOrder($blockOrder);
        $this->em->persist($content);
        $this->em->flush();

        return new OperationResult($content, $newIds);
    }
}
