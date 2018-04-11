<?php

namespace Perform\PageEditorBundle\EventListener;

use Perform\PageEditorBundle\Annotation\Page;
use Perform\PageEditorBundle\PageManager;
use Perform\PageEditorBundle\SessionManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

/**
 * Configures the PageManager according to the current request.
 *
 * If the SessionManager detects an editing session for the current request, enable edit mode.
 *
 * If the @Page annotation is applied to the controller action, set the page name.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class PageManagerListener
{
    protected $pageManager;
    protected $sessionManager;

    public function __construct(PageManager $pageManager, SessionManager $sessionManager)
    {
        $this->pageManager = $pageManager;
        $this->sessionManager = $sessionManager;
    }

    public function onKernelRequest(GetResponseEvent $event)
    {
        if (!$event->isMasterRequest()) {
            return;
        }
        if ($this->sessionManager->requestIsEditing($event->getRequest())) {
            $this->pageManager->enableEditMode();
        }
    }

    public function onKernelController(FilterControllerEvent $event)
    {
        $annotation = $event->getRequest()->attributes->get('_page');
        if ($annotation instanceof Page) {
            $this->pageManager->setCurrentPage($annotation->getPage());
        }
    }
}
