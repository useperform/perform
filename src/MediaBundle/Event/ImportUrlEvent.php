<?php

namespace Perform\MediaBundle\Event;

use Perform\MediaBundle\MediaResource;
use Symfony\Component\EventDispatcher\Event;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class ImportUrlEvent extends Event
{
    protected $url;
    protected $name;
    protected $owner;
    protected $bucketName;
    protected $resources = [];

    public function __construct($url, $name = null, User $owner = null, $bucketName = null)
    {
        $this->url = $url;
        $this->name = $name;
        $this->owner = $owner;
        $this->bucketName = $bucketName;
    }

    public function getUrl()
    {
        return $this->url;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getOwner()
    {
        return $this->owner;
    }

    public function addResource(MediaResource $resource)
    {
        $this->resources[] = $resource;
    }

    /**
     * @param Resources[] $resources
     *
     * @return ImportUrlEvent
     */
    public function setResources(array $resources)
    {
        $this->resources = $resources;

        return $this;
    }

    /**
     * @return Resources[]
     */
    public function getResources()
    {
        return $this->resources;
    }
}
