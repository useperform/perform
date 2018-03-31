<?php

namespace Perform\PageEditorBundle\Tests\EventListener;

use Doctrine\ORM\EntityManagerInterface;
use Perform\PageEditorBundle\EventListener\ToolbarListener;
use Perform\PageEditorBundle\Twig\Extension\ContentExtension;
use Perform\RichContentBundle\BlockType\BlockTypeRegistry;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;

/**
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
        $this->twig = $this->getMockBuilder(\Twig_Environment::class)
                    ->disableOriginalConstructor()
                    ->getMock();
        $this->contentExtension = $this->getMockBuilder(ContentExtension::class)
                                ->disableOriginalConstructor()
                                ->getMock();
        $repo = $this->getMockBuilder(VersionRepository::class)
              ->disableOriginalConstructor()
              ->getMock();
        $entityManager = $this->getMock(EntityManagerInterface::class);
        $entityManager->expects($this->any())
            ->method('getRepository')
            ->will($this->returnValue($repo));
        $registry = new BlockTypeRegistry($this->twig);

        $this->listener = new ToolbarListener($this->twig, $this->contentExtension, $entityManager, $registry);
        $this->kernel = $this->getMock(HttpKernelInterface::class);
    }

    public function testEventSubscriber()
    {
        $this->assertInstanceOf(EventSubscriberInterface::class, $this->listener);
        $this->assertSame(
            [
                KernelEvents::REQUEST => ['onKernelRequest', -128],
                KernelEvents::CONTROLLER => ['onKernelController', -128],
                KernelEvents::RESPONSE => ['onKernelResponse', -99],
            ],
            $this->listener->getSubscribedEvents());
    }

    protected function createRequest($withSession = true, $url = '/')
    {
        $request = Request::create($url);
        if ($withSession) {
            $session = $this->getMock(SessionInterface::class);
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
        $this->twig->expects($this->exactly(2))
            ->method('render')
            ->withConsecutive(
                $this->equalTo('@PerformPageEditor/stylesheets.html.twig'),
                $this->equalTo('@PerformPageEditor/toolbar.html.twig'))
            ->willReturnOnConsecutiveCalls('<link/>', '<div>TOOLBAR</div>');

        $event = $this->createEvent('<head></head><body><div>Hello</div></body>');
        $this->listener->onKernelResponse($event);
        $this->assertSame('<head><link/></head><body><div>Hello</div><div>TOOLBAR</div></body>', $event->getResponse()->getContent());
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

    public function excludedUrls()
    {
        return [
            ['/admin', ['/admin']],
            ['/admin/foo', ['^/admin']],
            ['/_profiler/123456', ['^/(_(profiler|wdt)|css|images|js)/']],
        ];
    }

    /**
     * @dataProvider excludedUrls
     */
    public function testExcludedUrlsAreNotPutIntoEditMode($url, array $regexes)
    {
        $request = $this->createRequest(true, $url);
        $event = new GetResponseEvent($this->kernel, $request, HttpKernelInterface::MASTER_REQUEST);
        $this->listener->setExcludedUrlRegexes($regexes);
        $this->contentExtension->expects($this->never())
            ->method('setMode');

        $this->listener->onKernelRequest($event);
    }
}
