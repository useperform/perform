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
            'crud' => 'perform_blog.markdown_post',
        ])->setExtra('icon', 'newspaper-o');
    }
}
