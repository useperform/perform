<?php

namespace Perform\EventsBundle\Menu;

use Knp\Menu\ItemInterface;
use Perform\Base\Menu\LinkProviderInterface;

/**
 * EventsLinkProvider.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class EventsLinkProvider implements LinkProviderInterface
{
    public function addLinks(ItemInterface $menu)
    {
        $menu->addChild('Events', [
            'route' => 'perform_events_events_list',
        ])->setExtra('icon', 'calendar');
    }
}
