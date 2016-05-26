<?php

namespace Admin\Base\Menu;

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
            'route' => 'admin_base_user_list',
        ])->setExtra('icon', 'users');
    }
}
