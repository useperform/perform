<?php

namespace Perform\CmsBundle\Locator;

use Perform\BaseBundle\Util\BundleSearcher;
use Doctrine\Common\Annotations\Reader;
use Perform\CmsBundle\Annotation\Page;

/**
 * PageLocator.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class PageLocator
{
    protected $searcher;
    protected $reader;

    public function __construct(BundleSearcher $searcher, Reader $reader)
    {
        $this->searcher = $searcher;
        $this->reader = $reader;
    }

    /**
     * Get an array of page names and their content sections.
     *
     * [
     * 'home' => ['main', 'sidebar'],
     * 'blog' => ['articles', 'sidebar'],
     * ]
     *
     * @return array
     */
    public function getPageNames()
    {
        $controllers = $this->searcher->findClassesInNamespaceSegment('Controller');
        $pages = [];

        foreach ($controllers as $class) {
            $r = new \ReflectionClass($class);
            $this->maybeAddAnnotation($this->reader->getClassAnnotation($r, Page::class), $pages);
            foreach ($r->getMethods() as $method) {
                $this->maybeAddAnnotation($this->reader->getMethodAnnotation($method, Page::class), $pages);
            }
        }

        return $pages;
    }

    protected function maybeAddAnnotation($annotation = null, array &$pages)
    {
        if (!$annotation) {
            return;
        }

        $page = $annotation->getPage();
        if (!isset($pages[$page])) {
            $pages[$page] = [];
        }

        $pages[$page] = array_merge($pages[$page], $annotation->getSections());
    }
}
