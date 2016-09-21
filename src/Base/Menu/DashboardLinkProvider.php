<?php

namespace Perform\Base\Menu;

use Knp\Menu\ItemInterface;

/**
 * DashboardLinkProvider
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class DashboardLinkProvider implements LinkProviderInterface
{
    public function addLinks(ItemInterface $menu)
    {
        $menu->addChild('Dashboard', [
            'route' => 'perform_base_dashboard_index',
        ])->setExtra('icon', 'dashboard');
    }
}
