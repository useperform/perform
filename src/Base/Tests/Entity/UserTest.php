<?php

namespace Admin\Base\Tests\Entity;

use Admin\Base\Entity\User;

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
}