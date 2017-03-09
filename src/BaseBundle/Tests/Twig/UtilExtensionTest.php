<?php

namespace Perform\BaseBundle\Tests\Twig;

use Perform\BaseBundle\Twig\Extension\UtilExtension;

/**
 * UtilExtensionTest.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class UtilExtensionTest extends \PHPUnit_Framework_TestCase
{
    protected $extension;

    public function setUp()
    {
        $this->extension = new UtilExtension();
    }

    public function testHumanDateNoDate()
    {
        $this->assertSame('', $this->extension->humanDate(null));
    }
}
