<?php

namespace Perform\BaseBundle\Tests\Crud;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class TestEntity
{
    protected $id;

    public function __construct($id)
    {
        $this->id = $id;
    }

    public function getId()
    {
        return $this->id;
    }
}
