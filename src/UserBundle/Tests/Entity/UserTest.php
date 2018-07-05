<?php

namespace Perform\UserBundle\Tests\Entity;

use Perform\UserBundle\Entity\User;

/**
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

    public function testRemoveRole()
    {
        $user = new User();
        $user->setRoles(['ROLE_USER', 'ROLE_ADMIN', 'ROLE_MANAGER', 'ROLE_EDITOR']);
        $user->removeRole('ROLE_ADMIN');
        $this->assertSame([0 => 'ROLE_USER', 1 => 'ROLE_MANAGER', 2 => 'ROLE_EDITOR'], $user->getRoles());

        //ignore not existing
        $user->removeRole('ROLE_FOO');
        $this->assertSame([0 => 'ROLE_USER', 1 => 'ROLE_MANAGER', 2 => 'ROLE_EDITOR'], $user->getRoles());

        $user->removeRole('ROLE_MANAGER');
        $this->assertSame([0 => 'ROLE_USER', 1 => 'ROLE_EDITOR'], $user->getRoles());
    }

    public function testHasRole()
    {
        $user = new User();
        $user->setRoles(['ROLE_USER', 'ROLE_ADMIN']);
        $this->assertTrue($user->hasRole('ROLE_USER'));
        $this->assertTrue($user->hasRole('ROLE_ADMIN'));
        $this->assertFalse($user->hasRole('ROLE_SUPER_ADMIN'));
    }
}
