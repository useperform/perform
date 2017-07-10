<?php

namespace Perform\RichContentBundle\Twig\Extension;

use Perform\RichContentBundle\Entity\Content;
use Perform\RichContentBundle\BlockType\TextBlockType;
use Perform\RichContentBundle\BlockType\ImageBlockType;
use Perform\RichContentBundle\Renderer\RendererInterface;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class ContentExtension extends \Twig_Extension
{
    protected $renderer;

    public function __construct(RendererInterface $renderer)
    {
        $this->renderer = $renderer;
    }

    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('perform_rich_content', [$this->renderer, 'render'], ['is_safe' => ['html']]),
        ];
    }
}
