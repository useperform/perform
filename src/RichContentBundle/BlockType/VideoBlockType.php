<?php

namespace Perform\RichContentBundle\BlockType;

use Perform\RichContentBundle\Entity\Block;

/**
 * Show videos from common services or the media library.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class VideoBlockType implements BlockTypeInterface
{
    public function render(Block $block)
    {
        $value = $block->getValue();
        if (!isset($value['type']) || !isset($value['id'])) {
            return '';
        }

        switch ($value['type']) {
        case 'youtube':
            return sprintf('<iframe src="https://www.youtube.com/embed/%s" frameBorder="0" allowFullScreen></iframe>', $value['id']);
        default:
            return '';
        }
    }
}
