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
    protected $deleteAfterProcess = false;

    public function __construct($path, $name = null, User $owner = null)
    {
        $this->path = $path;
        $this->name = $name ?: basename($path);
        $this->owner = $owner;
    }

    /**
     * @var string $path
     *
     * @return static
     */
    public function setPath($path)
    {
        $this->path = $path;

        return $this;
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

    public function deleteAfterProcess($delete = true)
    {
        $this->deleteAfterProcess = $delete;
    }

    /**
     * Delete this resource, but only if it has been marked to be
     * deleted after processing.
     *
     * Use deleteAfterProcess() to mark this resource for deletion.
     *
     * Nothing will happen if the resource is not a file.
     */
    public function delete()
    {
        if (!$this->deleteAfterProcess || !$this->isFile()) {
            return;
        }

        @unlink($this->path);
    }
}
