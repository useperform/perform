<?php

namespace Perform\PageEditorBundle\Menu;

use Knp\Menu\ItemInterface;
use Perform\BaseBundle\Menu\LinkProviderInterface;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class PageEditorLinkProvider implements LinkProviderInterface
{
    public function addLinks(ItemInterface $menu)
    {
        $menu->addChild('page_editor', [
            'route' => 'perform_pageeditor_session_begin',
        ])->setExtra('icon', 'pencil');
    }
}
