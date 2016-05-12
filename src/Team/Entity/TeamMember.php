<?php

namespace Admin\Team\Entity;

/**
 * TeamMember
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class TeamMember
{
    /**
     * @var uuid
     */
    protected $id;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $slug;

    /**
     * Role / job title in the team
     *
     * @var string
     */
    protected $role;

    /**
     * @var string
     */
    protected $description;

    /**
     * @var int
     */
    protected $sortOrder;

    /**
     * @return uuid
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $name
     *
     * @return TeamMember
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
     * @param string $slug
     *
     * @return TeamMember
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * @param string $role
     *
     * @return TeamMember
     */
    public function setRole($role)
    {
        $this->role = $role;

        return $this;
    }

    /**
     * @return string
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * @param string $description
     *
     * @return TeamMember
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param int $sortOrder
     *
     * @return TeamMember
     */
    public function setSortOrder($sortOrder)
    {
        $this->sortOrder = (int) $sortOrder;

        return $this;
    }

    /**
     * @return int
     */
    public function getSortOrder()
    {
        return $this->sortOrder;
    }
}
