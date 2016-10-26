<?php

namespace Perform\MusicBundle\Menu;

use Knp\Menu\ItemInterface;
use Perform\BaseBundle\Menu\LinkProviderInterface;

/**
 * CompositionLinkProvider.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class CompositionLinkProvider implements LinkProviderInterface
{
    public function addLinks(ItemInterface $menu)
    {
        $menu->addChild('compositions', [
            'route' => 'perform_music_composition_list',
        ])->setExtra('icon', 'music');
    }
}
