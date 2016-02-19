<?php

namespace Base\Tests\Admin;

use Admin\Base\Admin\UserAdmin;

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
        $this->assertInstanceOf('Admin\Base\Admin\AdminInterface', $this->admin);
    }
}
