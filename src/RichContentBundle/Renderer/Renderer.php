<?php

namespace Perform\RichContentBundle\Renderer;

use Perform\RichContentBundle\Entity\Content;
use Perform\RichContentBundle\BlockType\BlockTypeRegistry;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class Renderer implements RendererInterface
{
    protected $registry;

    public function __construct(BlockTypeRegistry $registry)
    {
        $this->registry = $registry;
    }

    public function render(Content $content)
    {
        $html = '';
        foreach ($content->getOrderedBlocks() as $block) {
            $html .= $this->registry->get($block->getType())->render($block);
        }

        return $html;
    }
}
