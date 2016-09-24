<?php

namespace Perform\CmsBundle\Menu;

use Knp\Menu\ItemInterface;
use Perform\BaseBundle\Menu\LinkProviderInterface;

/**
 * CmsLinkProvider.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class CmsLinkProvider implements LinkProviderInterface
{
    public function addLinks(ItemInterface $menu)
    {
        $menu->addChild('Page Editor', [
            'route' => 'perform_cms_session_begin',
        ])->setExtra('icon', 'pencil');
    }
}