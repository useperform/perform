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

    /**
     * @return string
     */
    public function getDescription();

    /**
     * Get the default value to be passed to a new instance of this block type.
     *
     * @return array
     */
    public function getDefaults();
}
