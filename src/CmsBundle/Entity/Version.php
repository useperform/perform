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
     * @var \Datetime
     */
    protected $createdAt;

    /**
     * @var \Datetime
     */
    protected $updatedAt;

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
     * @param \DateTime $createdAt
     *
     * @return Version
     */
    public function setCreatedAt(\DateTime $createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param \DateTime $updatedAt
     *
     * @return Version
     */
    public function setUpdatedAt(\DateTime $updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
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

    /**
     * Return an array representation of this version, child sections, and child
     * blocks of those sections.
     *
     * @return array
     */
    public function toArray()
    {
        $data = [];
        foreach ($this->sections as $section) {
            $name = $section->getName();
            $data[$name] = [];
            foreach ($section->getBlocks() as $block) {
                $data[$name][] = [
                    'type' => $block->getType(),
                    'value' => $block->getValue(),
                ];
            }
        }

        return $data;
    }
}
