<?php

namespace Perform\BaseBundle\Tests\Routing;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Request;
use Perform\BaseBundle\Routing\RequestInfo;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class RequestInfoTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->stack = $this->getMock(RequestStack::class);
        $this->info = new RequestInfo($this->stack);
    }

    public function testGetReferer()
    {
        $request = new Request();
        $request->headers->set('referer', '/previous-url');
        $this->stack->expects($this->any())
            ->method('getCurrentRequest')
            ->will($this->returnValue($request));

        $this->assertSame('/previous-url', $this->info->getReferer());
    }

    public function testGetRefererWithFallback()
    {
        $request = new Request();
        $this->stack->expects($this->any())
            ->method('getCurrentRequest')
            ->will($this->returnValue($request));

        $this->assertSame('/fallback', $this->info->getReferer('/fallback'));
    }

    public function testGetRefererWithDefaultFallback()
    {
        $request = new Request();
        $this->stack->expects($this->any())
            ->method('getCurrentRequest')
            ->will($this->returnValue($request));

        $this->assertSame('/', $this->info->getReferer());
    }
}
