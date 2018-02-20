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
    protected $locations;

    public function __construct()
    {
        $this->locations = new ArrayCollection();
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
    public function setPrimaryLocation(Location $location)
    {
        $location->setPrimary(true);
        $this->addLocation($location);

        return $this;
    }

    /**
     * @return Location
     */
    public function getPrimaryLocation()
    {
        foreach ($this->locations as $location) {
            if ($location->isPrimary()) {
                return $location;
            }
        }

        // getting to this stage suggests the location table is out of sync
        // always return a Location object to prevent things blowing up
        return Location::url('');
    }

    /**
     * @param Location $location
     *
     * @return static
     */
    public function addLocation(Location $location)
    {
        if (!$this->locations->contains($location)) {
            $this->locations[] = $location;
            $location->setFile($this);
        }

        return $this;
    }

    /**
     * @return Collection
     */
    public function getLocations()
    {
        return $this->locations;
    }

    /**
     * @return Collection
     */
    public function getExtraLocations()
    {
        // return the locations 0 indexed, so don't use collection->filter()
        return new ArrayCollection(array_values(
            array_filter($this->locations->toArray(), function($location) {
                return !$location->isPrimary();
            })
        ));
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
