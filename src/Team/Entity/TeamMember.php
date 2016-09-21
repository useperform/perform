<?php

namespace Perform\Team\Entity;
use Perform\MediaBundle\Entity\File;

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
     * @var \DateTime
     */
    protected $createdAt;

    /**
     * @var \DateTime
     */
    protected $updatedAt;

    /**
     * @var File
     */
    protected $image;

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

    /**
     * @param \DateTime $createdAt
     *
     * @return TeamMember
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
     * @return TeamMember
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
     * @param File|null $image
     *
     * @return TeamMember
     */
    public function setImage(File $image = null)
    {
        $this->image = $image;

        return $this;
    }

    /**
     * @return File
     */
    public function getImage()
    {
        return $this->image;
    }
}
