<?php

namespace Perform\CmsBundle\Annotation;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\ConfigurationInterface;

/**
 * Page
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 * @Annotation
 **/
class Page implements ConfigurationInterface
{
    protected $page;
    protected $sections;

    public function __construct(array $configuration)
    {
        $message = __CLASS__.' requires a page name as a string, with optional sections as an array (e.g. @Page("home", sections={"main", "aside"})).';
        if (!isset($configuration['value']) || !is_string($configuration['value'])) {
            throw new \InvalidArgumentException($message);
        }
        $sections = isset($configuration['sections']) ? $configuration['sections'] : [];
        if (!is_array($sections)) {
            throw new \InvalidArgumentException($message);
        }

        $this->page = $configuration['value'];
        $this->sections = $sections;
    }

    public function getPage()
    {
        return $this->page;
    }

    public function getSections()
    {
        return $this->sections;
    }

    public function getAliasName()
    {
        return 'page';
    }

    public function allowArray()
    {
        return false;
    }
}
