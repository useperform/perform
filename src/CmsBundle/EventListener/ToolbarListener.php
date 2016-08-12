<?php

namespace Admin\CmsBundle\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpFoundation\Request;
use Admin\CmsBundle\Twig\Extension\ContentExtension;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\Event\KernelEvent;
use Doctrine\ORM\EntityManagerInterface;
use Admin\CmsBundle\Annotation\Page;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Admin\CmsBundle\Block\BlockTypeRegistry;

/**
 * ToolbarListener.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class ToolbarListener implements EventSubscriberInterface
{
    const SESSION_KEY = 'cms_toolbar';

    protected $twig;
    protected $extension;
    protected $entityManager;
    protected $registry;
    protected $page;

    public function __construct(\Twig_Environment $twig, ContentExtension $extension, EntityManagerInterface $entityManager, BlockTypeRegistry $registry)
    {
        $this->twig = $twig;
        $this->extension = $extension;
        $this->entityManager = $entityManager;
        $this->registry = $registry;
    }

    protected function inEditMode(KernelEvent $event)
    {
        if (!$event->isMasterRequest()) {
            return false;
        }
        $request = $event->getRequest();
        if ($request->isXmlHttpRequest()) {
            return false;
        }
        $session = $request->getSession();
        if (!$session || $session->get(self::SESSION_KEY) !== true) {
            return false;
        }

        return true;
    }

    public function onKernelRequest(GetResponseEvent $event)
    {
        if (!$this->inEditMode($event)) {
            return;
        }

        $this->extension->setMode(ContentExtension::MODE_EDIT);
    }

    public function onKernelController(FilterControllerEvent $event)
    {
        $annotation = $event->getRequest()->attributes->get('_page');
        if (!$annotation instanceof Page) {
            return;
        }
        $this->extension->setPage($this->page = $annotation->getPage());
    }

    public function onKernelResponse(FilterResponseEvent $event)
    {
        if (!$this->inEditMode($event)) {
            return;
        }

        $response = $event->getResponse();
        $content = $response->getContent();
        $pos = strripos($content, '</body>');

        if (false === $pos) {
            return;
        }

        $repo = $this->entityManager->getRepository('AdminCmsBundle:Version');
        $versions = $this->page ? $repo->findByPage($this->page) : [];
        $current = $this->page ? $repo->findCurrentVersion($this->page) : null;

        $toolbar = $this->twig->render(
            'AdminCmsBundle::toolbar.html.twig',
            [
                'versions' => $versions,
                'currentVersion' => $current,
                'blockTypes' => $this->registry->getTypes(),
            ]
        );
        $content = substr($content, 0, $pos).$toolbar.substr($content, $pos);
        $response->setContent($content);
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::REQUEST => ['onKernelRequest', -128],
            KernelEvents::CONTROLLER => ['onKernelController', -128],
            //just before the profiler listener, to make sure resources used by
            //the toolbar are included
            KernelEvents::RESPONSE => ['onKernelResponse', -99],
        ];
    }
}
