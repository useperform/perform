<?php

namespace Admin\CmsBundle\Tests\EventListener;

use Admin\CmsBundle\EventListener\ToolbarListener;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpFoundation\Session\Session;

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

    protected function createEvent($content, $withSession = true)
    {
        $request = new Request();
        if ($withSession) {
            $session = $this->getMock('Symfony\Component\HttpFoundation\Session\SessionInterface');
            $session->expects($this->any())
                ->method('get')
                ->with(ToolbarListener::SESSION_KEY)
                ->will($this->returnValue(true));
            $request->setSession($session);
        }


        $response = new Response();
        $response->setContent($content);
        $event = new FilterResponseEvent($this->kernel, $request, HttpKernelInterface::MASTER_REQUEST, $response);

        return $event;
    }


    public function testToolbarIsAddedToWellFormedResponse()
    {
        $this->twig->expects($this->once())
            ->method('render')
            ->with('AdminCmsBundle::toolbar.html.twig')
            ->will($this->returnValue('<div>TOOLBAR</div>'));

        $event = $this->createEvent('<body><div>Hello</div></body>');
        $this->listener->onKernelResponse($event);
        $this->assertSame('<body><div>Hello</div><div>TOOLBAR</div></body>', $event->getResponse()->getContent());
    }

    public function testToolbarIsNotAddedToUnformedResponse()
    {
        $event = $this->createEvent('{"foo":"bar"}');
        $this->twig->expects($this->never())
            ->method('render');

        $this->listener->onKernelResponse($event);
        $this->assertSame('{"foo":"bar"}', $event->getResponse()->getContent());
    }

    public function testToolbarIsNotAddedWhenSessionNotSet()
    {
        $event = $this->createEvent('<body></body>', false);
        $this->twig->expects($this->never())
            ->method('render');

        $this->listener->onKernelResponse($event);
        $this->assertSame('<body></body>', $event->getResponse()->getContent());
    }
}
