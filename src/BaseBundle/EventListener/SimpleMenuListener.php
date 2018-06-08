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
    protected $priority;

    public function __construct($name, $crud = null, $route = null, $icon = null, $priority = 0)
    {
        if (!$crud && !$route) {
            throw new \InvalidArgumentException('A simple menu item requires either a crud name or route name.');
        }

        $this->name = $name;
        $this->crud = $crud;
        $this->route = $route;
        $this->icon = $icon;
        $this->priority = $priority;
    }

    public function onMenuBuild(MenuEvent $event)
    {
        if ($event->getMenuName() !== 'perform_sidebar') {
            return;
        }
        $menu = $event->getMenu();

        $options = $this->crud ? ['crud' => $this->crud] : ['route' => $this->route];
        $child = $menu->addChild($this->name, $options);

        if ($this->icon) {
            $child->setExtra('icon', $this->icon);
        }
        if ($this->priority !== 0) {
            $child->setExtra('priority', $this->priority);
        }
    }
}
