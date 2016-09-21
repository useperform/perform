<?php

namespace Perform\BaseBundle\Menu;

use Knp\Menu\FactoryInterface;

/**
 * MenuBuilder
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class MenuBuilder
{
    protected $factory;
    protected $providers = [];

    public function __construct(FactoryInterface $factory)
    {
        $this->factory = $factory;
    }

    public function addLinkProvider(LinkProviderInterface $linkProvider)
    {
        $this->providers[] = $linkProvider;
    }

    public function createMainMenu(array $options)
    {
        $menu = $this->factory->createItem('root');
        foreach ($this->providers as $provider) {
            $provider->addLinks($menu);
        }

        return $menu;
    }
}
