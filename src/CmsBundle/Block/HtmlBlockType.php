<?php

namespace Perform\CmsBundle\Block;

use Perform\CmsBundle\Entity\Block;

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

    public function getEditorTemplate()
    {
        return 'PerformCmsBundle:blocks:html.html.twig';
    }
}
