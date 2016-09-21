<?php

namespace Perform\BlogBundle\Menu;

use Knp\Menu\ItemInterface;
use Perform\Base\Menu\LinkProviderInterface;

/**
 * BlogLinkProvider.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class BlogLinkProvider implements LinkProviderInterface
{
    public function addLinks(ItemInterface $menu)
    {
        $menu->addChild('News', [
            'route' => 'perform_blog_post_list',
        ])->setExtra('icon', 'newspaper-o');
    }
}
