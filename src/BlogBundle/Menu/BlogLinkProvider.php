<?php

namespace Perform\BlogBundle\Menu;

use Knp\Menu\ItemInterface;
use Perform\BaseBundle\Menu\LinkProviderInterface;

/**
 * BlogLinkProvider.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class BlogLinkProvider implements LinkProviderInterface
{
    public function addLinks(ItemInterface $menu)
    {
        $menu->addChild('blog', [
            'entity' => 'PerformBlogBundle:MarkdownPost',
        ])->setExtra('icon', 'newspaper-o');
    }
}
