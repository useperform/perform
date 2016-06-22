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

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('content', [$this, 'getContent'], ['is_safe' => ['html']]),
        ];
    }

    public function getContent($page, $section)
    {
        $published = $this->entityManager
            ->getRepository('AdminCmsBundle:PublishedContent')
            ->findOneBy([
                'page' => $page,
                'section' => $section,
            ]);

        if (!$published) {
            return '';
        }

        return $published->getContent();
    }

    public function getName()
    {
        return 'cms';
    }
}
