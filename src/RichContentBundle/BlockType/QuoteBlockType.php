<?php

namespace Perform\RichContentBundle\BlockType;

use Perform\RichContentBundle\Entity\Block;

/**
 * Block type for quote with citations.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class QuoteBlockType implements BlockTypeInterface
{
    public function render(Block $block)
    {
        $value = $block->getValue();
        $text = isset($value['text']) ? $value['text'] : '';
        $cite = isset($value['cite']) ? $value['cite'] : '';

        return sprintf('<blockquote>%s<footer><cite>%s</cite></footer></blockquote>', $text, $cite);
    }
}
