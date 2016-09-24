<?php

namespace Perform\CmsBundle\Entity;

/**
 * Block.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class Block
{
    /**
     * @var uuid
     */
    protected $id;

    /**
     * @var string
     */
    protected $type;

    /**
     * @var int
     */
    protected $sortOrder;

    /**
     * @var array
     */
    protected $value;

    /**
     * @var Section
     */
    protected $section;

    /**
     * @return uuid
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $type
     *
     * @return Block
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param int $sortOrder
     *
     * @return Block
     */
    public function setSortOrder($sortOrder)
    {
        $this->sortOrder = $sortOrder;

        return $this;
    }

    /**
     * @return int
     */
    public function getSortOrder()
    {
        return $this->sortOrder;
    }

    /**
     * @param array $value
     *
     * @return Block
     */
    public function setValue(array $value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * @return array
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param Section $section
     *
     * @return Block
     */
    public function setSection(Section $section)
    {
        $this->section = $section;

        return $this;
    }

    /**
     * @return Section
     */
    public function getSection()
    {
        return $this->section;
    }
}
