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
    protected $entity;
    protected $route;
    protected $icon;

    public function __construct($alias, $entity = null, $route = null, $icon = null)
    {
        $this->alias = $alias;
        $this->entity = $entity;
        $this->route = $route;
        $this->icon = $icon;
    }

    public function addLinks(ItemInterface $menu)
    {
        if ($this->entity) {
            $child = $menu->addChild($this->alias, [
                'entity' => $this->entity,
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
