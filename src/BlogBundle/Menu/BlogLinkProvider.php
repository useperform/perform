<?php

namespace Admin\BlogBundle\Menu;

use Knp\Menu\ItemInterface;
use Admin\Base\Menu\LinkProviderInterface;

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
            'route' => 'admin_blog_post_list',
        ])->setExtra('icon', 'newspaper-o');
    }
}
