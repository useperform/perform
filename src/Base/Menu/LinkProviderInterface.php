<?php

namespace Perform\Base\Menu;

use Knp\Menu\ItemInterface;

/**
 * LinkProviderInterface
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
interface LinkProviderInterface
{
    public function addLinks(ItemInterface $menu);
}
