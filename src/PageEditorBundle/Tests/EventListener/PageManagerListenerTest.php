<?php

namespace Perform\PageEditorBundle\Tests\EventListener;

use PHPUnit\Framework\TestCase;
use Perform\PageEditorBundle\Annotation\Page;
use Perform\PageEditorBundle\EventListener\PageManagerListener;
use Perform\PageEditorBundle\PageManager;
use Perform\PageEditorBundle\SessionManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class PageManagerListenerTest extends TestCase
{
    protected $pageManager;
    protected $sessionManager;
    protected $listener;
    protected $kernel;

    public function setUp()
    {
        $this->pageManager = $this->getMockBuilder(PageManager::class)
                           ->disableOriginalConstructor()
                           ->getMock();
        $this->sessionManager = $this->getMockBuilder(SessionManager::class)
                              ->disableOriginalConstructor()
                              ->getMock();
        $this->listener = new PageManagerListener($this->pageManager, $this->sessionManager);
        $this->kernel = $this->createMock(HttpKernelInterface::class);
    }

    public function testEditingSessionEnablesEditMode()
    {
        $this->sessionManager->expects($this->any())
            ->method('requestIsEditing')
            ->with($request = new Request())
            ->will($this->returnValue(true));
        $this->pageManager->expects($this->once())
            ->method('enableEditMode');

        $this->listener->onKernelRequest(new GetResponseEvent($this->kernel, $request, HttpKernelInterface::MASTER_REQUEST));
    }

    public function testNoSessionDoesNotEnableEditMode()
    {
        $this->sessionManager->expects($this->any())
            ->method('requestIsEditing')
            ->with($request = new Request())
            ->will($this->returnValue(false));
        $this->pageManager->expects($this->never())
            ->method('enableEditMode');

        $this->listener->onKernelRequest(new GetResponseEvent($this->kernel, $request, HttpKernelInterface::MASTER_REQUEST));
    }

    public function testSubRequestsAreIgnored()
    {
        $this->sessionManager->expects($this->never())
            ->method('requestIsEditing');
        $this->pageManager->expects($this->never())
            ->method('enableEditMode');

        $this->listener->onKernelRequest(new GetResponseEvent($this->kernel, $request = new Request(), HttpKernelInterface::SUB_REQUEST));
    }

    public function testPageAnnotationIsDetected()
    {
        $request = new Request();
        $request->attributes->set('_page', new Page(['value' => 'home']));
        $this->pageManager->expects($this->once())
            ->method('setCurrentPage')
            ->with('home');
        $this->listener->onKernelController(new FilterControllerEvent($this->kernel, function () {}, $request, HttpKernelInterface::MASTER_REQUEST));
    }

    public function testPageIsNotSetForNoAnnotation()
    {
        $this->pageManager->expects($this->never())
            ->method('setCurrentPage');
        $this->listener->onKernelController(new FilterControllerEvent($this->kernel, function () {}, new Request(), HttpKernelInterface::MASTER_REQUEST));
    }
}
