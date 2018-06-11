<?php

namespace Perform\BaseBundle\Tests\Routing;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Perform\BaseBundle\Routing\RouteChecker;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class RouteCheckerTest extends \PHPUnit_Framework_TestCase
{
    protected $urlGenerator;
    protected $checker;

    public function setUp()
    {
        $this->urlGenerator = $this->getMock(UrlGeneratorInterface::class);
        $this->checker = new RouteChecker($this->urlGenerator);
    }

    public function testRouteExists()
    {
        $this->assertTrue($this->checker->routeExists('some_route'));
    }

    public function testRouteDoesNotExist()
    {
        $this->urlGenerator->expects($this->any())
            ->method('generate')
            ->with('some_route')
            ->will($this->throwException(new RouteNotFoundException()));

        $this->assertFalse($this->checker->routeExists('some_route'));
    }
}
