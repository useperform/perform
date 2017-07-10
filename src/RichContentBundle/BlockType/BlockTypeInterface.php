<?php

namespace Perform\RichContentBundle\BlockType;

use Perform\RichContentBundle\Entity\Block;

/**
 * BlockTypeInterface
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
interface BlockTypeInterface
{
    /**
     * Transform a block entity of this type into HTML content.
     *
     * @param Block
     *
     * @return string
     */
    public function render(Block $block);

    //RichContentBundle\Config\BlockTypeConfig?
    // public function configure();
}
