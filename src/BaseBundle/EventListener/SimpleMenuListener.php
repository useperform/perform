<?php

namespace Perform\BaseBundle\EventListener;

use Perform\BaseBundle\Event\MenuEvent;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class SimpleMenuListener
{
    protected $name;
    protected $crud;
    protected $route;
    protected $icon;

    public function __construct($name, $crud = null, $route = null, $icon = null)
    {
        if (!$crud && !$route) {
            throw new \InvalidArgumentException('A simple menu item requires either a crud name or route name.');
        }

        $this->name = $name;
        $this->crud = $crud;
        $this->route = $route;
        $this->icon = $icon;
    }

    public function onMenuBuild(MenuEvent $event)
    {
        if ($event->getMenuName() !== 'perform_sidebar') {
            return;
        }
        $menu = $event->getMenu();

        if ($this->crud) {
            $child = $menu->addChild($this->name, [
                'crud' => $this->crud,
            ]);
        } else {
            $child = $menu->addChild($this->name, [
                'route' => $this->route,
            ]);
        }

        if ($this->icon) {
            $child->setExtra('icon', $this->icon);
        }
    }
}
