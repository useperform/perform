<?php

namespace Perform\BaseBundle\Menu;

use Knp\Menu\FactoryInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Perform\BaseBundle\Event\MenuEvent;

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

        return $menu;
    }
}
