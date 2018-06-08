<?php

namespace Perform\BaseBundle\Menu;

use Knp\Menu\FactoryInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Perform\BaseBundle\Event\MenuEvent;
use Knp\Menu\ItemInterface;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class MenuBuilder
{
    protected $factory;
    protected $dispatcher;

    public function __construct(FactoryInterface $factory, EventDispatcherInterface $dispatcher)
    {
        $this->factory = $factory;
        $this->dispatcher = $dispatcher;
    }

    public function createSidebar(array $options)
    {
        $menu = $this->factory->createItem('root');

        $this->dispatcher->dispatch(MenuEvent::BUILD, new MenuEvent('perform_sidebar', $menu, $this->factory));

        $this->sortByPriority($menu);

        return $menu;
    }

    public function sortByPriority(ItemInterface $menu)
    {
        $order = [];
        foreach ($menu as $name => $item) {
            $order[$name] = $item->getExtra('priority', 0);

            if ($item->hasChildren()) {
                $this->sortByPriority($item);
            }
        }

        arsort($order);

        $menu->reorderChildren(array_keys($order));
    }
}
