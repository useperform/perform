<?php

namespace Perform\MediaBundle;

use Perform\UserBundle\Entity\User;

/**
 * A resource to be imported into the media library.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class MediaResource
{
    protected $path;
    protected $name;
    protected $owner;

    public function __construct($path, $name = null, User $owner = null)
    {
        $this->path = $path;
        $this->name = $name ?: basename($path);
        $this->owner = $owner;
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
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
    public function isFile()
    {
        return file_exists($this->path);
    }
}
