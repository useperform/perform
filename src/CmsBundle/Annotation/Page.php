<?php

namespace Admin\CmsBundle\Annotation;

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

    public function __construct(array $configuration)
    {
        if (!isset($configuration['value'])) {
            throw new \InvalidArgumentException(__CLASS__.' requires a page name.');
        }

        $this->page = $configuration['value'];
    }

    public function getPage()
    {
        return $this->page;
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
