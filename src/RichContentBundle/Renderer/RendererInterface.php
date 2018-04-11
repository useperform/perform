<?php

namespace Perform\RichContentBundle\Renderer;

use Perform\RichContentBundle\Entity\Content;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
interface RendererInterface
{
    /**
     * Transform a content entity into HTML content.
     *
     * @param Block
     *
     * @return string
     */
    public function render(Content $content = null);
}
