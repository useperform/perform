<?php

namespace Perform\BlogBundle\Twig\Extension;

use Symfony\Component\Form\FormFactoryInterface;
use Doctrine\ORM\EntityManagerInterface;

/**
 * BlogExtension
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class BlogExtension extends \Twig_Extension
{
    protected $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('perform_recent_blog_posts', [$this, 'getPosts']),
            new \Twig_SimpleFunction('perform_blog_published_tags', [$this, 'getTags']),
        ];
    }

    public function getPosts($limit = 5)
    {
        return $this->entityManager
            ->getRepository('PerformBlogBundle:Post')
            ->findRecent($limit);
    }

    public function getTags()
    {
        return $this->entityManager
            ->getRepository('PerformBlogBundle:Tag')
            ->findPublished();
    }

    public function getName()
    {
        return 'blog';
    }
}
