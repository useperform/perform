<?php

namespace Perform\MediaBundle\Menu;

use Knp\Menu\ItemInterface;
use Perform\Base\Menu\LinkProviderInterface;

/**
 * MediaLinkProvider.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class MediaLinkProvider implements LinkProviderInterface
{
    public function addLinks(ItemInterface $menu)
    {
        $media = $menu->addChild('Media', [
            'uri' => '#'
        ])->setExtra('icon', 'briefcase');
        $media->addChild('List', [
            'route' => 'admin_media_file_list'
        ]);
        $media->addChild('Import', [
            'route' => 'admin_media_file_upload'
        ]);
    }
}
