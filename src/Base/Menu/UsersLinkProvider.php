<?php

namespace Perform\Base\Menu;

use Knp\Menu\ItemInterface;

/**
 * UsersLinkProvider
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class UsersLinkProvider implements LinkProviderInterface
{
    public function addLinks(ItemInterface $menu)
    {
        $menu->addChild('Users', [
            'route' => 'perform_base_user_list',
        ])->setExtra('icon', 'users');
    }
}
