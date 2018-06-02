<?php

namespace Perform\BaseBundle\Menu;

use Knp\Menu\ItemInterface;

/**
 * SimpleLinkProvider.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class SimpleLinkProvider implements LinkProviderInterface
{
    protected $alias;
    protected $crud;
    protected $route;
    protected $icon;

    public function __construct($alias, $crud = null, $route = null, $icon = null)
    {
        if (!$crud && !$route) {
            throw new \InvalidArgumentException('A simple menu requires either a crud name or route name.');
        }

        $this->alias = $alias;
        $this->crud = $crud;
        $this->route = $route;
        $this->icon = $icon;
    }

    public function addLinks(ItemInterface $menu)
    {
        if ($this->crud) {
            $child = $menu->addChild($this->alias, [
                'crud' => $this->crud,
            ]);
        } else {
            $child = $menu->addChild($this->alias, [
                'route' => $this->route,
            ]);
        }

        if ($this->icon) {
            $child->setExtra('icon', $this->icon);
        }
    }
}
