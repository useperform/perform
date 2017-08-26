<?php

namespace Perform\UserBundle\Tests\Admin;

use Perform\UserBundle\Admin\UserAdmin;

/**
 * UserAdminTest
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class UserAdminTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->admin = new UserAdmin();
    }

    public function testIsAdmin()
    {
        $this->assertInstanceOf('Perform\BaseBundle\Admin\AdminInterface', $this->admin);
    }
}
