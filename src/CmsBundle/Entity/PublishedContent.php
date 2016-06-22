<?php

namespace Admin\CmsBundle\Entity;

/**
 * PublishedContent
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class PublishedContent
{
    /**
     * @var uuid
     */
    protected $id;

    /**
     * @var string
     */
    protected $page;

    /**
     * @var string
     */
    protected $section;

    /**
     * @var string
     */
    protected $content;

    /**
     * @return uuid
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $page
     *
     * @return PublishedContent
     */
    public function setPage($page)
    {
        $this->page = $page;

        return $this;
    }

    /**
     * @return string
     */
    public function getPage()
    {
        return $this->page;
    }

    /**
     * @param string $section
     *
     * @return PublishedContent
     */
    public function setSection($section)
    {
        $this->section = $section;

        return $this;
    }

    /**
     * @return string
     */
    public function getSection()
    {
        return $this->section;
    }

    /**
     * @param string $content
     *
     * @return PublishedContent
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }
}
