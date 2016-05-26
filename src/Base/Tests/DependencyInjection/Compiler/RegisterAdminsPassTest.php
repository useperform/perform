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

    public function testIsCompilerPass()
    {
        $this->assertInstanceOf('Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface', $this->pass);
    }
}
