<?php

namespace Perform\MediaPlayerBundle\Menu;

use Knp\Menu\ItemInterface;
use Perform\BaseBundle\Menu\LinkProviderInterface;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class PlaylistLinkProvider implements LinkProviderInterface
{
    public function addLinks(ItemInterface $menu)
    {
        $menu->addChild('playlists', [
            'route' => 'perform_mediaplayer_admin_list',
        ])->setExtra('icon', 'headphones');
    }
}
