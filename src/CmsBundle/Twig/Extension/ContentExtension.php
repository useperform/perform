<?php

namespace Admin\CmsBundle\Twig\Extension;

use Doctrine\ORM\EntityManagerInterface;

/**
 * ContentExtension.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class ContentExtension extends \Twig_Extension
{
    protected $entityManager;
    protected $twig;
    protected $mode = self::MODE_VIEW;
    protected $page;

    const MODE_VIEW = 0;
    const MODE_EDIT = 1;

    public function __construct(EntityManagerInterface $entityManager, \Twig_Environment $twig)
    {
        $this->entityManager = $entityManager;
        $this->twig = $twig;
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
            new \Twig_SimpleFunction('content', [$this, 'getContent'], ['is_safe' => ['html']]),
        ];
    }

    public function getContent($sectionName, $page = null)
    {
        if ($this->mode === self::MODE_EDIT) {
            return $this->createEditorSection($sectionName);
        }

        $page = $page ?: $this->page;
        if (!$page) {
            throw new \Exception(sprintf('Page name must be declared to load content.'));
        }

        return $this->getPublishedContent($page, $sectionName);
    }

    protected function getPublishedContent($page, $sectionName)
    {
        $published = $this->entityManager
                   ->getRepository('AdminCmsBundle:PublishedSection')
                   ->findOneBy([
                       'page' => $page,
                       'name' => $sectionName,
                   ]);

        if (!$published) {
            throw new \Exception(sprintf('Published section "%s" not found for page "%s".', $sectionName, $page));
        }

        return $published->getContent();
    }

    protected function createEditorSection($sectionName)
    {
        return $this->twig->render('AdminCmsBundle::section.html.twig', [
            'sectionName' => $sectionName,
        ]);
    }

    public function getName()
    {
        return 'cms';
    }
}
