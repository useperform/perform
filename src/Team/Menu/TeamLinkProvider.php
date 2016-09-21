<?php

namespace Perform\Team\Menu;

use Knp\Menu\ItemInterface;
use Perform\Base\Menu\LinkProviderInterface;

/**
 * TeamLinkProvider.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class TeamLinkProvider implements LinkProviderInterface
{
    public function addLinks(ItemInterface $menu)
    {
        $menu->addChild('Members', [
            'route' => 'perform_team_team_list',
        ])->setExtra('icon', 'users');
    }
}
