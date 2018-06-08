<?php

namespace Perform\MailingListBundle\EventListener;

use Knp\Menu\ItemInterface;
use Perform\BaseBundle\Menu\LinkProviderInterface;
use Perform\BaseBundle\Event\MenuEvent;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class MailingListMenuListener
{
    public function onMenuBuild(MenuEvent $event)
    {
        if ($event->getMenuName() !== 'perform_sidebar') {
            return;
        }
        $menu = $event->getMenu();

        $parent = $menu->addChild('mailing_list.main', [
            'uri' => '#'
        ])->setExtra('icon', 'users');

        $parent->addChild('mailing_list.lists', [
            'route' => 'perform_mailing_list_local_list_list',
        ]);
        $parent->addChild('mailing_list.subscribers', [
            'route' => 'perform_mailing_list_local_subscriber_list',
        ]);
    }
}
