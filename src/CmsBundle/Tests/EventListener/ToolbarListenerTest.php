<?php

namespace Admin\CmsBundle\Tests\EventListener;

use Admin\CmsBundle\EventListener\ToolbarListener;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Admin\CmsBundle\Twig\Extension\ContentExtension;

/**
 * ToolbarListenerTest.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class ToolbarListenerTest extends \PHPUnit_Framework_TestCase
{
    protected $twig;
    protected $contentExtension;
    protected $listener;
    protected $kernel;

    public function setUp()
    {
        $this->twig = $this->getMock('\Twig_Environment');
        $this->contentExtension = $this->getMockBuilder('Admin\CmsBundle\Twig\Extension\ContentExtension')
                                ->disableOriginalConstructor()
                                ->getMock();
        $repo = $this->getMockBuilder('Admin\CmsBundle\Repository\VersionRepository')
              ->disableOriginalConstructor()
              ->getMock();
        $entityManager = $this->getMock('Doctrine\ORM\EntityManagerInterface');
        $entityManager->expects($this->any())
            ->method('getRepository')
            ->will($this->returnValue($repo));
        $registry = $this->getMock('Admin\CmsBundle\Block\BlockTypeRegistry');

        $this->listener = new ToolbarListener($this->twig, $this->contentExtension, $entityManager, $registry);
        $this->kernel = $this->getMock('Symfony\Component\HttpKernel\HttpKernelInterface');
    }

    public function testEventSubscriber()
    {
        $this->assertInstanceOf('Symfony\Component\EventDispatcher\EventSubscriberInterface', $this->listener);
        $this->assertSame(
            [
                KernelEvents::REQUEST => ['onKernelRequest', -128],
                KernelEvents::CONTROLLER => ['onKernelController', -128],
                KernelEvents::RESPONSE => ['onKernelResponse', -99],
            ],
            $this->listener->getSubscribedEvents());
    }

    protected function createRequest($withSession = true)
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

        return $request;
    }

    protected function createEvent($content, $withSession = true)
    {
        $request = $this->createRequest($withSession);
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

    public function testContentExtensionPutIntoEditMode()
    {
        $event = new GetResponseEvent($this->kernel, $this->createRequest(), HttpKernelInterface::MASTER_REQUEST);
        $this->contentExtension->expects($this->once())
            ->method('setMode')
            ->with(ContentExtension::MODE_EDIT);

        $this->listener->onKernelRequest($event);
    }

    public function testContentExtensionNotPutIntoEditMode()
    {
        $event = new GetResponseEvent($this->kernel, $this->createRequest(false), HttpKernelInterface::MASTER_REQUEST);
        $this->contentExtension->expects($this->never())
            ->method('setMode');

        $this->listener->onKernelRequest($event);
    }
}
