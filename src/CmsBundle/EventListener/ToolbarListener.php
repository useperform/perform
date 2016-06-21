<?php

namespace Admin\CmsBundle\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * ToolbarListener.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class ToolbarListener implements EventSubscriberInterface
{
    const SESSION_KEY = 'cms_toolbar';

    protected $twig;

    public function __construct(\Twig_Environment $twig)
    {
        $this->twig = $twig;
    }

    public function onKernelResponse(FilterResponseEvent $event)
    {
        if (!$event->isMasterRequest()) {
            return;
        }

        $request = $event->getRequest();
        if ($request->isXmlHttpRequest()) {
            return;
        }
        $session = $request->getSession();
        if (!$session || $session->get(self::SESSION_KEY) !== true) {
            return;
        }

        $response = $event->getResponse();
        $content = $response->getContent();
        $pos = strripos($content, '</body>');

        if (false === $pos) {
            return;
        }
        $toolbar = $this->twig->render(
            'AdminCmsBundle::toolbar.html.twig',
            []
        );
        $content = substr($content, 0, $pos).$toolbar.substr($content, $pos);
        $response->setContent($content);
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::RESPONSE => ['onKernelResponse', -128],
        ];
    }
}
