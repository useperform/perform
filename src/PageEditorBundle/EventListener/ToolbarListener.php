<?php

namespace Perform\PageEditorBundle\EventListener;

use Doctrine\ORM\EntityManagerInterface;
use Perform\PageEditorBundle\Annotation\Page;
use Perform\PageEditorBundle\Twig\Extension\ContentExtension;
use Perform\RichContentBundle\BlockType\BlockTypeRegistry;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\Event\KernelEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class ToolbarListener implements EventSubscriberInterface
{
    const SESSION_KEY = 'perform_page_editor_toolbar';

    protected $twig;
    protected $extension;
    protected $entityManager;
    protected $registry;
    protected $page;
    protected $sections = [];
    protected $excludedUrlRegexes = [];

    public function __construct(\Twig_Environment $twig, ContentExtension $extension, EntityManagerInterface $entityManager, BlockTypeRegistry $registry)
    {
        $this->twig = $twig;
        $this->extension = $extension;
        $this->entityManager = $entityManager;
        $this->registry = $registry;
    }

    public function setExcludedUrlRegexes(array $regexes)
    {
        $this->excludedUrlRegexes = $regexes;
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
        $url = $request->getPathinfo();
        foreach ($this->excludedUrlRegexes as $regex) {
            if (preg_match('`'.$regex.'`', $url)) {
                return;
            }
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
        $this->sections = $annotation->getSections();
    }

    public function onKernelResponse(FilterResponseEvent $event)
    {
        if (!$this->inEditMode($event)) {
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

        $pageAvailable = $this->page && !empty($this->sections);

        $repo = $this->entityManager->getRepository('PerformPageEditorBundle:Version');
        $versions = $pageAvailable ? $repo->findByPage($this->page) : [];
        $current = $pageAvailable ? $repo->findCurrentVersion($this->page) : null;

        $html = $this->twig->render(
            '@PerformPageEditor/toolbar.html.twig',
            [
                'versions' => $versions,
                'currentVersion' => $current,
                'registry' => $this->registry,
            ]
        );
        $content = substr($content, 0, $pos).$html.substr($content, $pos);
        $response->setContent($content);
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::REQUEST => ['onKernelRequest', -128],
            KernelEvents::CONTROLLER => ['onKernelController', -128],
            //just before the profiler listener, to make sure resources used by
            //the toolbar are included in the profiler
            KernelEvents::RESPONSE => ['onKernelResponse', -99],
        ];
    }
}
