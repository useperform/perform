<?php

namespace Perform\BaseBundle\EventListener;

use Perform\BaseBundle\Event\MenuEvent;
use Perform\BaseBundle\Routing\RouteChecker;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class SettingsMenuListener
{
    protected $routeChecker;

    public function __construct(RouteChecker $routeChecker)
    {
        $this->routeChecker = $routeChecker;
    }

    public function onMenuBuild(MenuEvent $event)
    {
        if ($event->getMenuName() !== 'perform_sidebar') {
            return;
        }

        if (!$this->routeChecker->routeExists('perform_base_settings_settings')) {
            return;
        }

        $menu = $event->getMenu();

        $menu->addChild('settings', [
            'route' => 'perform_base_settings_settings',
        ])
            ->setExtra('icon', 'cogs')
            ->setExtra('priority', -10);
    }
}
