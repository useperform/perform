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
     * Update content using data sent from the frontend editor.
     */
    public function saveFromEditor(Content $content, array $blockDefinitions, array $newBlockDefinitions, array $blockOrder)
    {
        $this->em->transactional(function () use ($content, $blockDefinitions, $newBlockDefinitions, $blockOrder) {
            $newBlocks = $this->blockRepo->createFromDefinitions($newBlockDefinitions);
            $currentBlocks = $this->blockRepo->updateFromDefinitions($blockDefinitions);
            $blocks = array_merge($newBlocks, $currentBlocks);

            $this->contentRepo->setBlocks($content, $blocks);
            $content->setBlockOrder($blockOrder);

            $this->em->persist($content);
            $this->em->flush();
        });
    }
}
