<?php

namespace Perform\MediaBundle\Menu;

use Knp\Menu\ItemInterface;
use Perform\BaseBundle\Menu\LinkProviderInterface;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class MediaLinkProvider implements LinkProviderInterface
{
    public function addLinks(ItemInterface $menu)
    {
        $media = $menu->addChild('media.main', [
            'route' => 'perform_media_app_index'
        ])->setExtra('icon', 'briefcase');
    }
}
