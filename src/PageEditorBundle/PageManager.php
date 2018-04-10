<?php

namespace Perform\PageEditorBundle;

use Doctrine\ORM\EntityManagerInterface;
use Perform\PageEditorBundle\Entity\Section;
use Perform\PageEditorBundle\Entity\Version;
use Perform\RichContentBundle\Entity\Content;
use Perform\RichContentBundle\Renderer\RendererInterface;

/**
 * Renders page content fragments.
 *
 * The manager can be put into 'edit mode', where the content will be
 * rendered using the rich content editor, allowing it to be updated
 * directly on the page.
 *
 * The current version must be set before rendering, either by setting
 * the page name with setCurrentPage(), or the version object itself
 * with setCurrentVersion().
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
    protected $currentVersion;

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

    /**
     * @param string $currentPage
     */
    public function setCurrentPage($currentPage)
    {
        $this->currentPage = $currentPage;
        $this->currentVersion = null;
    }

    /**
     * @return string
     */
    public function getCurrentPage()
    {
        return $this->currentPage;
    }

    public function setCurrentVersion(Version $version)
    {
        $this->currentVersion = $version;
        $this->currentPage = $version->getPage();
    }

    public function getCurrentVersion()
    {
        if ($this->currentVersion) {
            return $this->currentVersion;
        }

        if (!$this->currentPage) {
            throw new \Exception(sprintf('The current page name must declared to render content.'));
        }

        return $this->entityManager
            ->getRepository('PerformPageEditorBundle:Version')
            ->findDefaultVersion($this->currentPage);
    }

    public function render($sectionName)
    {
        return $this->inEditMode() ?
            $this->renderEditorSection($sectionName) :
            $this->renderPublishedSection($sectionName);
    }

    public function renderPublishedSection($sectionName)
    {
        $version = $this->getCurrentVersion();
        $section = $version->getSection($sectionName);

        if (!$section) {
            // throw exception for debug, recover gracefully for prod
            $section = $this->createBlankSection($version, $sectionName);
        }

        return $this->renderer->render($section->getContent());
    }

    protected function createBlankSection(Version $version, $name)
    {
        $section = new Section();
        $section->setName($name);
        $content = new Content();
        $content->setTitle('');
        $section->setContent($content);
        $section->setVersion($version);
        $this->entityManager->persist($content);
        $this->entityManager->persist($section);
        $this->entityManager->flush();

        return $section;
    }

    protected function renderEditorSection($sectionName)
    {
        $version = $this->getCurrentVersion();
        $section = $version->getSection($sectionName) ?: $this->createBlankSection($version, $sectionName);

        return $this->twig->render('@PerformPageEditor/section.html.twig', [
            'section' => $section,
        ]);
    }
}
