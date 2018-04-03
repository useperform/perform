<?php

namespace Perform\PageEditorBundle\EventListener;

use Perform\PageEditorBundle\Annotation\Page;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Perform\PageEditorBundle\PageManager;

/**
 * Injects the toolbar if the PageManager is in edit mode for this request.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class ToolbarListener
{
    protected $pageManager;
    protected $twig;

    public function __construct(PageManager $pageManager, \Twig_Environment $twig)
    {
        $this->pageManager = $pageManager;
        $this->twig = $twig;
    }

    public function onKernelResponse(FilterResponseEvent $event)
    {
        if (!$this->pageManager->inEditMode()) {
            return;
        }

        $response = $event->getResponse();
        $this->injectCss($response);
        $this->injectToolbar($response);
    }

    protected function injectCss(Response $response)
    {
        $content = $response->getContent();
        $pos = stripos($content, '</head>');

        if (false === $pos) {
            return;
        }

        $html = $this->twig->render('@PerformPageEditor/stylesheets.html.twig', []);
        $content = substr($content, 0, $pos).$html.substr($content, $pos);
        $response->setContent($content);
    }

    protected function injectToolbar(Response $response)
    {
        $content = $response->getContent();
        $pos = strripos($content, '</body>');

        if (false === $pos) {
            return;
        }

        $html = $this->twig->render('@PerformPageEditor/toolbar.html.twig');
        $content = substr($content, 0, $pos).$html.substr($content, $pos);
        $response->setContent($content);
    }
}
