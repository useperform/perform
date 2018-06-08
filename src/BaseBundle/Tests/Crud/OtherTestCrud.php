<?php

namespace Perform\BaseBundle\Tests\Crud;

use Perform\BaseBundle\Crud\AbstractCrud;
use Perform\BaseBundle\Config\FieldConfig;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class OtherTestCrud extends AbstractCrud
{
    public function configureFields(FieldConfig $config)
    {
    }

    public static function getEntityClass()
    {
        return OtherTestEntity::class;
    }
}
