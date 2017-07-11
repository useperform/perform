<?php

namespace Perform\RichContentBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Perform\RichContentBundle\Entity\Content;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class ContentRepository extends EntityRepository
{
    /**
     * Declare the blocks associated with a piece of content.
     *
     * Any blocks currently associated with the content that are not
     * in the supplied blocks will be dissociated.
     * Any of the dissociated blocks that were only linked to this
     * piece of content will be removed from the database.
     *
     * @param Content
     * @param Block[] $blocks
     */
    public function setBlocks(Content $content, array $blocks)
    {
        $currentBlocks = $content->getBlocks();

        foreach ($blocks as $block) {
            if (!$currentBlocks->contains($block)) {
                $currentBlocks->add($block);
            }
        }

        foreach ($currentBlocks as $block) {
            if (in_array($block, $blocks)) {
                continue;
            }
            $currentBlocks->removeElement($block);
        }

        $this->_em->persist($content);
        $this->_em->flush();

        // check and remove newly orphaned blocks
    }
}
