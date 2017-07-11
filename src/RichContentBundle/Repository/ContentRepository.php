<?php

namespace Perform\RichContentBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Perform\RichContentBundle\Entity\Content;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class ContentRepository extends EntityRepository
{
    public function updateContent(Content $content, array $blockDefinitions, array $blockOrder)
    {
        $currentBlocks = $content->getBlocks();

        foreach ($currentBlocks as $block) {
            if (!isset($blockDefinitions[$block->getId()])) {
                continue;
                // remove blocks not present in definitions
            }
            $def = $blockDefinitions[$block->getId()];
            $block->setValue($def['value']);
            $this->_em->persist($block);

            unset($blockDefinitions[$block->getId()]);
        }
        //any definitions left are new blocks to be created

        $content->setBlockOrder($blockOrder);
        $this->_em->persist($content);
        $this->_em->flush();
    }
}
