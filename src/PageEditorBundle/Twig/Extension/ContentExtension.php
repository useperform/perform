<?php

namespace Perform\PageEditorBundle\Twig\Extension;

use Perform\PageEditorBundle\PageManager;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class ContentExtension extends \Twig_Extension
{
    protected $pageManager;

    public function __construct(PageManager $pageManager)
    {
        $this->pageManager = $pageManager;
    }

    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('perform_page_editor_content', [$this->pageManager, 'render'], ['is_safe' => ['html']]),
        ];
    }

}
