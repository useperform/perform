<?php

namespace Perform\BaseBundle\Tests\Twig;

use Perform\BaseBundle\Twig\Extension\UtilExtension;
use Perform\BaseBundle\Routing\RouteChecker;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class UtilExtensionTest extends \PHPUnit_Framework_TestCase
{
    protected $extension;

    public function setUp()
    {
        $this->checker = $this->getMockBuilder(RouteChecker::class)
                       ->disableOriginalConstructor()
                       ->getMock();
        $this->extension = new UtilExtension($this->checker);
    }

    public function testHumanDateNoDate()
    {
        $this->assertSame('', $this->extension->humanDate(null));
    }
}
