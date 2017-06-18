<?php

namespace Perform\BaseBundle\Tests\Fixtures\ExtendEntities\YamlChildBundle\Entity;

use Perform\BaseBundle\Tests\Fixtures\ExtendEntities\YamlParentBundle\Entity\Item;

/**
 * YamlItem
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class YamlItem extends Item
{
    protected $extraField;
}
