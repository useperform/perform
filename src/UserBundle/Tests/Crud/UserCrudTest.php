<?php

namespace Perform\UserBundle\Tests\Crud;

use Perform\UserBundle\Crud\UserCrud;
use Perform\BaseBundle\Crud\CrudInterface;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class UserCrudTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->crud = new UserCrud();
    }

    public function testIsCrud()
    {
        $this->assertInstanceOf(CrudInterface::class, $this->crud);
    }
}
