<?php

namespace Admin\ContactBundle\Menu;

use Knp\Menu\ItemInterface;
use Admin\Base\Menu\LinkProviderInterface;

/**
 * ContactLinkProvider.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class ContactLinkProvider implements LinkProviderInterface
{
    public function addLinks(ItemInterface $menu)
    {
        $menu->addChild('Contact Form', [
            'route' => 'admin_contact_message_list',
        ])->setExtra('icon', 'envelope');
    }
}
