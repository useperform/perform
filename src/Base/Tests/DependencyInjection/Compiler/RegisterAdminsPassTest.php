<?php

namespace Base\Tests\DependencyInjection\Compiler;

use Admin\Base\DependencyInjection\Compiler\RegisterAdminsPass;

/**
 * RegisterAdminsPassTest
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class RegisterAdminsPassTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->pass = new RegisterAdminsPass();
    }

    public function guessEntityProvider()
    {
        return [
            ['AdminBaseBundle:User', 'Admin\BaseBundle\Entity\User'],
        ];
    }

    /**
     * @dataProvider guessEntityProvider
     */
    public function testGuessEntityClass($alias, $expected)
    {
        $this->assertSame($expected, $this->pass->guessEntityClass($alias));
    }
}
