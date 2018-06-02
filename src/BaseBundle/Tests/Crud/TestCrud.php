<?php

namespace Perform\BaseBundle\Tests\Crud;

use Perform\BaseBundle\Crud\AbstractCrud;
use Perform\BaseBundle\Config\TypeConfig;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class TestCrud extends AbstractCrud
{
    public function configureTypes(TypeConfig $config)
    {
    }

    public static function getEntityClass()
    {
        return TestEntity::class;
    }
}
