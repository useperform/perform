<?php

namespace Perform\PageEditorBundle;

use Perform\PageEditorBundle\Entity\Section;
use Perform\RichContentBundle\Entity\Content;
use Doctrine\ORM\EntityManagerInterface;
use Perform\RichContentBundle\Renderer\RendererInterface;

/**
 * Renders page content fragments.
 *
 * The manager can be put into 'edit mode', where the content will be
 * rendered using the rich content editor, allowing it to be updated
 * directly on the page.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class PageManager
{
    protected $entityManager;
    protected $twig;
    protected $renderer;
    protected $editMode;
    protected $currentPage;

    public function __construct(EntityManagerInterface $entityManager, \Twig_Environment $twig, RendererInterface $renderer)
    {
        $this->entityManager = $entityManager;
        $this->twig = $twig;
        $this->renderer = $renderer;
    }

    /**
     * Enable 'edit mode' for the current request.
     */
    public function enableEditMode($enabled = true)
    {
        $this->editMode = (bool) $enabled;
    }

    public function inEditMode()
    {
        return (bool) $this->editMode;
    }

    public function setCurrentPage($currentPage)
    {
        $this->currentPage = $currentPage;
    }

    protected function ensureCurrentPage()
    {
        if (!$this->currentPage) {
            throw new \Exception(sprintf('The current page name must declared to render content.'));
        }
    }

    public function render($sectionName)
    {
        return $this->inEditMode() ?
            $this->renderEditorSection($sectionName) :
            $this->renderPublishedSection($sectionName);
    }

    public function renderPublishedSection($sectionName)
    {
        $this->ensureCurrentPage();
        $version = $this->getPublishedVersion($this->currentPage);
        $section = $version->getSection($sectionName);

        if (!$section) {
            // throw exception for debug, recover gracefully for prod
            $section = new Section();
            $section->setName($sectionName);
            $content = new Content();
            $content->setTitle('');
            $section->setContent($content);
            $version->addSection($section);
            $this->entityManager->persist($content);
            $this->entityManager->persist($section);
            $this->entityManager->flush();

        }

        return $this->renderer->render($section->getContent());
    }

    protected function renderEditorSection($sectionName)
    {
        $this->ensureCurrentPage();
        $version = $this->getPublishedVersion($this->currentPage);
        $section = $version->getSection($sectionName);

        if (!$section) {
            $section = new Section();
            $section->setName($sectionName);
            $content = new Content();
            $content->setTitle('');
            $section->setContent($content);
            $version->addSection($section);
            $this->entityManager->persist($content);
            $this->entityManager->persist($section);
            $this->entityManager->flush();
        }

        return $this->twig->render('@PerformPageEditor/section.html.twig', [
            'section' => $section,
        ]);
    }

    protected function getPublishedVersion($page)
    {
        // use version in memory if already fetched
        return $this->entityManager
            ->getRepository('PerformPageEditorBundle:Version')
            ->findCurrentVersion($page);
    }
}
