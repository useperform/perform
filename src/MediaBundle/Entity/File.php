<?php

namespace Perform\MediaBundle\Entity;

use Perform\UserBundle\Entity\User;
use Perform\MediaBundle\Location\Location;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class File
{
    /**
     * @var guid
     */
    protected $id;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $locationPath;

    /**
     * @var int
     */
    protected $locationType;

    /**
     * @var string
     */
    protected $bucketName;

    /**
     * @var string
     */
    protected $type;

    /**
     * @var array
     */
    protected $typeOptions = [];

    /**
     * @var string
     */
    protected $mimeType;

    /**
     * @var string
     */
    protected $charset;

    /**
     * @var \DateTime
     */
    protected $createdAt;

    /**
     * @var \DateTime
     */
    protected $updatedAt;

    /**
     * @var User
     */
    protected $owner;

    /**
     * @param guid $id
     *
     * @return File
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return guid
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $name
     *
     * @return File
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
     * @param Location $location
     *
     * @return File
     */
    public function setLocation(Location $location)
    {
        $this->locationPath = $location->getPath();
        $this->locationType = $location->getType();

        return $this;
    }

    /**
     * @return Location
     */
    public function getLocation()
    {
        return new Location($this->locationPath, $this->locationType);
    }

    /**
     * @param string $bucketName
     *
     * @return File
     */
    public function setBucketName($bucketName)
    {
        $this->bucketName = $bucketName;

        return $this;
    }

    /**
     * @return string
     */
    public function getBucketName()
    {
        return $this->bucketName;
    }

    /**
     * @param string $type
     *
     * @return File
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

    public function hasType()
    {
        return !!$this->type;
    }

    /**
     * @param array $typeOptions
     *
     * @return File
     */
    public function setTypeOptions(array $typeOptions)
    {
        $this->typeOptions = $typeOptions;

        return $this;
    }

    /**
     * @return array
     */
    public function getTypeOptions()
    {
        return $this->typeOptions;
    }

    /**
     * @param string $mimeType
     *
     * @return File
     */
    public function setMimeType($mimeType)
    {
        $this->mimeType = $mimeType;

        return $this;
    }

    /**
     * @return string
     */
    public function getMimeType()
    {
        return $this->mimeType;
    }

    /**
     * @param string $charset
     *
     * @return File
     */
    public function setCharset($charset)
    {
        $this->charset = $charset;

        return $this;
    }

    /**
     * @return string
     */
    public function getCharset()
    {
        return $this->charset;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * @param User|null $owner
     *
     * @return File
     */
    public function setOwner(User $owner)
    {
        $this->owner = $owner;

        return $this;
    }

    /**
     * @return User|null
     */
    public function getOwner()
    {
        return $this->owner;
    }

    /**
     * @return bool
     */
    public function hasOwner()
    {
        return $this->owner instanceof User;
    }
}
