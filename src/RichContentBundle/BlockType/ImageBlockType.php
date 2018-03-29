<?php

namespace Perform\RichContentBundle\BlockType;

use Perform\RichContentBundle\Entity\Block;

/**
 * Block type for displaying images.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class ImageBlockType implements BlockTypeInterface
{
    public function render(Block $block)
    {
        $value = $block->getValue();
        if (!isset($value['src'])) {
            return '';
        }

        return sprintf('<img src="%s" />', $value['src']);
    }

    public function getDescription()
    {
        return 'Images from the media library.';
    }

    public function getDefaults()
    {
        return [
            'src' => null,
        ];
    }
}
