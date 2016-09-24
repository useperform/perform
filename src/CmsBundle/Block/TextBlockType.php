<?php

namespace Perform\CmsBundle\Block;

use Perform\CmsBundle\Entity\Block;

/**
 * TextBlockType
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class TextBlockType implements BlockTypeInterface
{
    public function render(Block $block)
    {
        $value = $block->getValue();

        return sprintf('<p>%s</p>', isset($value['content']) ?
                       htmlspecialchars($value['content'])  : '');
    }

    public function getEditorTemplate()
    {
        return 'PerformCmsBundle:blocks:text.html.twig';
    }
}
