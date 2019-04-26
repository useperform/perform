<?php

namespace Perform\BaseBundle\Tests\Routing;

use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Request;
use Perform\BaseBundle\Routing\RequestInfo;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class RequestInfoTest extends TestCase
{
    public function setUp()
    {
        $this->stack = $this->createMock(RequestStack::class);
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

    public function testSameRefererAsCurrentRequestIsIgnored()
    {
        $request = Request::create('https://example.com/some-url');
        $request->headers->set('referer', 'https://example.com/some-url');
        $this->stack->expects($this->any())
            ->method('getCurrentRequest')
            ->will($this->returnValue($request));

        $this->assertSame('/fallback', $this->info->getReferer('/fallback'));
    }
}
