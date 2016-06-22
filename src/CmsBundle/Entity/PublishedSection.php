<?php

namespace Admin\CmsBundle\Entity;

/**
 * PublishedSection
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class PublishedSection
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
    protected $name;

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
     * @return PublishedSection
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
     * @param string $name
     *
     * @return PublishedSection
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $content
     *
     * @return PublishedSection
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
