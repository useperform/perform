<?php

namespace Perform\MediaBundle\Menu;

use Knp\Menu\ItemInterface;
use Perform\BaseBundle\Menu\LinkProviderInterface;

/**
 * MediaLinkProvider.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class MediaLinkProvider implements LinkProviderInterface
{
    public function addLinks(ItemInterface $menu)
    {
        $media = $menu->addChild('media.main', [
            'uri' => '#'
        ])->setExtra('icon', 'briefcase');
        $media->addChild('media.list', [
            'route' => 'perform_media_file_list'
        ]);
        $media->addChild('media.add', [
            'route' => 'perform_media_file_upload'
        ]);
    }
}
