<?php

namespace Admin\CmsBundle\Block;

use Admin\CmsBundle\Entity\Block;

/**
 * BlockTypeInterface
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
interface BlockTypeInterface
{
    /**
     * Transform a block entity of this type into content.
     *
     * @param Block
     *
     * @return string
     */
    public function render(Block $block);

    /**
     * @return string
     */
    public function getEditorTemplate();
}
