<?php

namespace Perform\PageEditorBundle\Twig\Extension;

use Doctrine\ORM\EntityManagerInterface;
use Perform\PageEditorBundle\Entity\Section;
use Perform\RichContentBundle\Entity\Content;
use Perform\RichContentBundle\Renderer\RendererInterface;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class ContentExtension extends \Twig_Extension
{
    protected $entityManager;
    protected $twig;
    protected $renderer;
    protected $mode = self::MODE_VIEW;
    protected $page;

    const MODE_VIEW = 0;
    const MODE_EDIT = 1;

    public function __construct(EntityManagerInterface $entityManager, \Twig_Environment $twig, RendererInterface $renderer)
    {
        $this->entityManager = $entityManager;
        $this->twig = $twig;
        $this->renderer = $renderer;
    }

    public function setMode($mode)
    {
        $this->mode = $mode;
    }

    public function setPage($page)
    {
        $this->page = $page;
    }

    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('perform_page_editor_content', [$this, 'render'], ['is_safe' => ['html']]),
        ];
    }

    public function render($sectionName, $page = null)
    {
        $page = $page ?: $this->page;
        if (!$page) {
            throw new \Exception(sprintf('Page name must be declared to render content.'));
        }

        if ($this->mode === self::MODE_EDIT) {
            return $this->renderEditorSection($page, $sectionName);
        }

        return $this->renderPublishedSection($page, $sectionName);
    }

    protected function renderPublishedSection($page, $sectionName)
    {
        $version = $this->getPublishedVersion($page);
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

            // throw new \Exception(sprintf('Published section "%s" not found for page "%s".', $sectionName, $page));
        }

        return $this->renderer->render($section->getContent());
    }

    protected function renderEditorSection($page, $sectionName)
    {
        $version = $this->getPublishedVersion($page);
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
            'page' => $page,
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
