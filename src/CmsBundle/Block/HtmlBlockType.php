<?php

namespace Admin\CmsBundle\Block;

use Admin\CmsBundle\Entity\Block;

/**
 * HtmlBlockType
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class HtmlBlockType implements BlockTypeInterface
{
    public function render(Block $block)
    {
        $value = $block->getValue();

        return isset($value['content']) ? $value['content'] : '';
    }
}
