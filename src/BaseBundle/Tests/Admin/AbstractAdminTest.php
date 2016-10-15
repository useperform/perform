<?php

namespace Perform\BaseBundle\Tests\Admin;

use Perform\BaseBundle\Admin\UserAdmin;

/**
 * AbstractAdminTest
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class AbstractAdminTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->admin = new TestAdmin();
    }

    public function testIsAdmin()
    {
        $this->assertInstanceOf('Perform\BaseBundle\Admin\AdminInterface', $this->admin);
    }
}
