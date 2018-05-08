<?php

namespace Perform\PageEditorBundle\Tests\EventListener;

use Perform\PageEditorBundle\EventListener\ToolbarListener;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Perform\PageEditorBundle\PageManager;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class ToolbarListenerTest extends \PHPUnit_Framework_TestCase
{
    protected $pageManager;
    protected $twig;
    protected $listener;
    protected $kernel;

    public function setUp()
    {
        $this->pageManager = $this->getMockBuilder(PageManager::class)
                           ->disableOriginalConstructor()
                           ->getMock();
        $this->twig = $this->getMockBuilder(\Twig_Environment::class)
                    ->disableOriginalConstructor()
                    ->getMock();
        $this->listener = new ToolbarListener($this->pageManager, $this->twig);
        $this->kernel = $this->getMock(HttpKernelInterface::class);
    }

    protected function createEvent($content)
    {
        $response = new Response();
        $response->setContent($content);

        return new FilterResponseEvent($this->kernel, new Request(), HttpKernelInterface::MASTER_REQUEST, $response);
    }

    public function testToolbarIsAddedToWellFormedResponse()
    {
        $this->pageManager->expects($this->any())
            ->method('inEditMode')
            ->will($this->returnValue(true));
        $this->pageManager->expects($this->any())
            ->method('hasCurrentPage')
            ->will($this->returnValue(true));

        $this->twig->expects($this->exactly(2))
            ->method('render')
            ->withConsecutive(
                $this->equalTo('@PerformPageEditor/stylesheets.html.twig'),
                $this->equalTo('@PerformPageEditor/toolbar.html.twig'))
            ->willReturnOnConsecutiveCalls('<link/>', '<div>TOOLBAR</div>');

        $initialContent = '<head></head><body><div>Hello</div></body>';
        $modifiedContent = '<head><link/></head><body><div>Hello</div><div>TOOLBAR</div></body>';
        $this->listener->onKernelResponse($event = $this->createEvent($initialContent));

        $this->assertSame($modifiedContent, $event->getResponse()->getContent());
    }

    public function testToolbarIsNotAddedToUnformedResponse()
    {
        $this->twig->expects($this->never())
            ->method('render');
        $content = '{"foo":"bar"}';
        $this->listener->onKernelResponse($event = $this->createEvent($content));

        $this->assertSame($content, $event->getResponse()->getContent());
    }

    public function testToolbarIsNotAddedWhenNotInEditMode()
    {
        $this->twig->expects($this->never())
            ->method('render');
        $content = '<head></head><body>Page</body>';
        $this->listener->onKernelResponse($event = $this->createEvent($content));

        $this->assertSame($content, $event->getResponse()->getContent());
    }
}
