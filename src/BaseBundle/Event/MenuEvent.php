<?php

namespace Perform\BaseBundle\Event;

use Symfony\Component\EventDispatcher\Event;
use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class MenuEvent extends Event
{
    const BUILD = 'perform_base.menu.build';

    /**
     * @var string The name of the menu
     */
    protected $menuName;

    /**
     * @var ItemInterface
     */
    protected $menu;

    /**
     * @var FactoryInterface
     */
    protected $factory;

    public function __construct($menuName, ItemInterface $menu, FactoryInterface $factory)
    {
        $this->menuName = $menuName;
        $this->menu = $menu;
        $this->factory = $factory;
    }

    public function getMenuName()
    {
        return $this->menuName;
    }

    public function getMenu()
    {
        return $this->menu;
    }

    public function getFactory()
    {
        return $this->factory;
    }
}
