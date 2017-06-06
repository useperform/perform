<?php

namespace Perform\MailingListBundle\Menu;

use Knp\Menu\ItemInterface;
use Perform\BaseBundle\Menu\LinkProviderInterface;

/**
 * MailingListLinkProvider.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class MailingListLinkProvider implements LinkProviderInterface
{
    public function addLinks(ItemInterface $menu)
    {
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
