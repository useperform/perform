<?php

namespace Admin\CmsBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * Version.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class Version
{
    /**
     * @var uuid
     */
    protected $id;

    /**
     * @var string
     */
    protected $title;

    /**
     * @var string
     */
    protected $page;

    /**
     * @var bool
     */
    protected $published = false;

    /**
     * @var Collection
     */
    protected $sections;

    public function __construct()
    {
        $this->sections = new ArrayCollection();
    }

    /**
     * @return uuid
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $title
     *
     * @return Version
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $page
     *
     * @return Version
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
     * @param bool $published
     *
     * @return Version
     */
    public function setPublished($published)
    {
        $this->published = $published;

        return $this;
    }

    /**
     * @return bool
     */
    public function isPublished()
    {
        return $this->published;
    }

    /**
     * @param Section $section
     *
     * @return Version
     */
    public function addSection(Section $section)
    {
        $this->sections[] = $section;
        $section->setVersion($this);

        return $this;
    }

    /**
     * @return Collection
     */
    public function getSections()
    {
        return $this->sections;
    }
}
