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
        case 'vimeo':
            return sprintf('<iframe src="https://player.vimeo.com/video/%s" frameBorder="0" allowFullScreen></iframe>', $value['id']);
        default:
            return '';
        }
    }

    public function getDescription()
    {
        return 'Embed a video from youtube, vimeo, or the media library.';
    }

    public function getDefaults()
    {
        return [];
    }

    public function getComponentInfo(Block $block)
    {
        return [];
    }
}
