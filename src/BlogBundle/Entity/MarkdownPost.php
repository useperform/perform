<?php

namespace Perform\BlogBundle\Entity;

use Perform\BaseBundle\Entity\TaggableTrait;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * A blog post written in markdown.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class MarkdownPost extends AbstractPost
{
    use TaggableTrait;

    /**
     * @var string
     */
    protected $markdown;

    public function __construct()
    {
        $this->tags = new ArrayCollection();
    }

    /**
     * @param string $markdown
     *
     * @return MarkdownPost
     */
    public function setMarkdown($markdown)
    {
        $this->markdown = $markdown;

        return $this;
    }

    /**
     * @return string
     */
    public function getMarkdown()
    {
        return $this->markdown;
    }
}
