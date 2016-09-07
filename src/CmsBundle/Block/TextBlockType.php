<?php

namespace Admin\CmsBundle\Block;

use Admin\CmsBundle\Entity\Block;

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
        return 'AdminCmsBundle:blocks:text.html.twig';
    }
}
