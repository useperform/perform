<?php

namespace Perform\ContactBundle\Menu;

use Knp\Menu\ItemInterface;
use Perform\BaseBundle\Menu\LinkProviderInterface;

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
            'route' => 'perform_contact_message_list',
        ])->setExtra('icon', 'envelope');
    }
}
