<?php

namespace Perform\MediaPlayerBundle\Menu;

use Knp\Menu\ItemInterface;
use Perform\BaseBundle\Menu\LinkProviderInterface;

/**
 * PlaylistLinkProvider.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class PlaylistLinkProvider implements LinkProviderInterface
{
    public function addLinks(ItemInterface $menu)
    {
        $menu->addChild('playlists', [
            'route' => 'perform_media_player_playlist_list',
        ])->setExtra('icon', 'headphones');
    }
}
