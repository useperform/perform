<?php

namespace Perform\BaseBundle\Tests\Crud;

use Perform\BaseBundle\Crud\AbstractCrud;
use Perform\BaseBundle\Config\TypeConfig;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class InvalidCrud extends AbstractCrud
{
    public function configureTypes(TypeConfig $config)
    {
    }

    public static function getEntityClass()
    {
        return 'Perform\Not\An\Entity';
    }
}
