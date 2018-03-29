<?php

namespace Perform\RichContentBundle\BlockType;

use Perform\RichContentBundle\Entity\Block;

/**
 * Block type for fragments of text.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class TextBlockType implements BlockTypeInterface
{
    public function render(Block $block)
    {
        $value = $block->getValue();
        if (!isset($value['content'])) {
            return '';
        }

        return sprintf('<p>%s</p>', $value['content']);
    }

    public function getDescription()
    {
        return 'Words and paragraphs.';
    }

    public function getDefaults()
    {
        return [
            'content' => '',
        ];
    }

    public function getComponentInfo(Block $block)
    {
        return [];
    }
}
