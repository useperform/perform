<?php

namespace Perform\BaseBundle\Tests\Entity;

use Perform\BaseBundle\Entity\User;

/**
 * UserTest
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class UserTest extends \PHPUnit_Framework_TestCase
{
    public function testGetFullname()
    {
        $user = new User();
        $user->setForename('Glynn');
        $user->setSurname('Forrest');
        $this->assertSame('Glynn Forrest', $user->getFullname());
    }

    public function testDefaultRoles()
    {
        $user = new User();
        $this->assertSame(['ROLE_USER'], $user->getRoles());
    }

    public function testAddRole()
    {
        $user = new User();
        $user->addRole('ROLE_ADMIN');
        $this->assertSame(['ROLE_USER', 'ROLE_ADMIN'], $user->getRoles());
        //ignore duplicates
        $user->addRole('ROLE_ADMIN');
        $this->assertSame(['ROLE_USER', 'ROLE_ADMIN'], $user->getRoles());
    }
}
