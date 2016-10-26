<?php

namespace Perform\BaseBundle\Menu;

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
        $menu->addChild('settings', [
            'route' => 'perform_base_settings_settings',
        ])->setExtra('icon', 'cogs');
    }
}
