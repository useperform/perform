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
        $menu->addChild('Mailing List Subscribers', [
            'route' => 'perform_mailing_list_subscriber_list',
        ])->setExtra('icon', 'users');
    }
}
