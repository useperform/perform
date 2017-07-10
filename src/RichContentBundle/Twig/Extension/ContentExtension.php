<?php

namespace Perform\RichContentBundle\Twig\Extension;

use Perform\RichContentBundle\Entity\Content;
use Perform\RichContentBundle\BlockType\TextBlockType;
use Perform\RichContentBundle\BlockType\ImageBlockType;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class ContentExtension extends \Twig_Extension
{
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('perform_rich_content', [$this, 'renderContent'], ['is_safe' => ['html']]),
        ];
    }

    public function renderContent(Content $content)
    {
        $html = '';
        $types = [
            'Text' => new TextBlockType(),
            'Image' => new ImageBlockType(),
        ];
        foreach ($content->getOrderedBlocks() as $block) {
            $html .= $types[$block->getType()]->render($block);
        }

        return $html;
    }
}
