<?php

namespace Perform\MediaBundle\Entity;

use Perform\UserBundle\Entity\User;
use Perform\MediaBundle\MediaResource;
use Doctrine\Common\Collections\ArrayCollection;

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
    protected $bucketName;

    /**
     * @var string
     */
    protected $type;

    /**
     * @var int
     */
    protected $status;

    // newly added, not processed yet
    const STATUS_NEW = 0;

    // processed successfully
    const STATUS_OK = 1;

    // processing had errors, reprocess is required
    const STATUS_ERROR = 2;

    /**
     * @var string
     */
    protected $locationPath;

    /**
     * @var int
     */
    protected $locationType;

    /**
     * @var array
     */
    protected $locationAttributes = [];

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
     * @var Collection
     */
    protected $extraLocations;

    public function __construct()
    {
        $this->extraLocations = new ArrayCollection();
    }

    public static function fromResource(MediaResource $resource)
    {
        $file = new self();
        $file->setName($resource->getName());
        $file->setOwner($resource->getOwner());

        return $file;
    }

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
     * @return static
     */
    public function setLocation(Location $location)
    {
        $this->locationPath = $location->getPath();
        $this->locationType = $location->getType();
        $this->locationAttributes = $location->getAttributes();

        return $this;
    }

    /**
     * @return Location
     */
    public function getLocation()
    {
        return new Location($this->locationPath, $this->locationType, $this->locationAttributes);
    }

    /**
     * @param string $name
     * @param mixed $value
     *
     * @return static
     */
    public function setLocationAttribute($name, $value)
    {
        $this->locationAttributes[$name] = $value;

        return $this;
    }

    /**
     * @param Location $location
     *
     * @return static
     */
    public function addExtraLocation(Location $location)
    {
        if (!$this->extraLocations->contains($location)) {
            $this->extraLocations[] = $location;
            $location->setFile($this);
        }

        return $this;
    }

    /**
     * @return Collection
     */
    public function getExtraLocations()
    {
        return $this->extraLocations;
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
     * @param int $status
     *
     * @return File
     */
    public function setStatus($status)
    {
        $this->status = (int) $status;

        return $this;
    }

    /**
     * @return int
     */
    public function getStatus()
    {
        return $this->status;
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
    public function setOwner(User $owner = null)
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
