<?php

namespace Admin\CmsBundle\Tests\EventListener;

use Admin\CmsBundle\EventListener\ToolbarListener;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * ToolbarListenerTest.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class ToolbarListenerTest extends \PHPUnit_Framework_TestCase
{
    protected $twig;
    protected $listener;
    protected $kernel;

    public function setUp()
    {
        $this->twig = $this->getMock('\Twig_Environment');
        $this->listener = new ToolbarListener($this->twig);
        $this->kernel = $this->getMock('Symfony\Component\HttpKernel\HttpKernelInterface');
    }

    public function testEventSubscriber()
    {
        $this->assertInstanceOf('Symfony\Component\EventDispatcher\EventSubscriberInterface', $this->listener);
        $this->assertSame(
            [
                KernelEvents::RESPONSE => ['onKernelResponse', -128],
            ],
            $this->listener->getSubscribedEvents());
    }

    public function testToolbarIsAddedToWellFormedResponse()
    {
        $request = new Request();
        $response = new Response();
        $response->setContent('<body><div>Hello</div></body>');
        $event = new FilterResponseEvent($this->kernel, $request, HttpKernelInterface::MASTER_REQUEST, $response);
        $this->twig->expects($this->once())
            ->method('render')
            ->with('AdminCmsBundle::toolbar.html.twig')
            ->will($this->returnValue('<div>TOOLBAR</div>'));

        $this->listener->onKernelResponse($event);
        $this->assertSame('<body><div>Hello</div><div>TOOLBAR</div></body>', $response->getContent());
    }

    public function testToolbarIsNotAddedToUnformedResponse()
    {
        $request = new Request();
        $response = new Response();
        $response->setContent('{"foo":"bar"}');
        $event = new FilterResponseEvent($this->kernel, $request, HttpKernelInterface::MASTER_REQUEST, $response);
        $this->twig->expects($this->never())
            ->method('render');

        $this->listener->onKernelResponse($event);
        $this->assertSame('{"foo":"bar"}', $response->getContent());
    }
}
