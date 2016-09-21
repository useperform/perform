<?php

namespace Perform\Base\Tests\Admin;

use Perform\Base\Admin\UserAdmin;

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
        $this->assertInstanceOf('Perform\Base\Admin\AdminInterface', $this->admin);
    }
}
