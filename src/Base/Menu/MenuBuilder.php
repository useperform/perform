<?php

namespace Admin\Base\Menu;

use Knp\Menu\FactoryInterface;

/**
 * MenuBuilder
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class MenuBuilder
{
    protected $factory;

    public function __construct(FactoryInterface $factory)
    {
        $this->factory = $factory;
    }

    public function createMainMenu(array $options)
    {
        $menu = $this->factory->createItem('root');

        $menu->addChild('Dashboard', [
            'route' => 'admin_base_dashboard_index',
        ])->setExtra('icon', 'dashboard');

        $media = $menu->addChild('Media', [
            'uri' => '#'
        ])->setExtra('icon', 'briefcase');
        $media->addChild('List', [
                'route' => 'admin_media_file_list'
            ]);
        $media->addChild('Import', [
                'route' => 'admin_media_file_upload'
            ]);

        $menu->addChild('Members', [
            'route' => 'admin_team_team_list',
        ])->setExtra('icon', 'users');

        //add menu builders to this class in a compiler pass
        //loop through each
        //call createMenu() on each

        return $menu;
    }
}
