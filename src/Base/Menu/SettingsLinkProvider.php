<?php

namespace Admin\Base\Menu;

use Knp\Menu\ItemInterface;

/**
 * SettingsLinkProvider
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class SettingsLinkProvider implements LinkProviderInterface
{
    public function addLinks(ItemInterface $menu)
    {
        $menu->addChild('Settings', [
            'route' => 'admin_base_settings_settings',
        ])->setExtra('icon', 'cogs');
    }
}
