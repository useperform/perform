<?php

namespace Perform\MediaBundle;

use Perform\UserBundle\Entity\User;
use Perform\MediaBundle\File\ParseResult;

/**
 * A resource to be imported into the media library.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class MediaResource
{
    protected $path;
    protected $isFile = false;
    protected $name;
    protected $owner;
    protected $deleteAfterProcess = false;
    protected $parseResult;

    public function __construct($path, $name = null, User $owner = null)
    {
        $this->path = $path;
        $this->name = $name ?: basename($path);
        $this->owner = $owner;
    }

    /**
     * Create a new resource, marking it as a file.
     */
    public static function file($path, $name = null, User $owner = null)
    {
        $resource = new self($path, $name, $owner);
        $resource->setFile(true);

        return $resource;
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
     * Mark this resource as a file.
     *
     * @param bool $isFile
     *
     * @return static
     */
    public function setFile($isFile)
    {
        $this->isFile = (bool) $isFile;

        return $this;
    }

    /**
     * @return bool
     */
    public function isFile()
    {
        return $this->isFile;
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

    /**
     * @param ParseResult $parseResult
     *
     * @return MediaResource
     */
    public function setParseResult(ParseResult $parseResult)
    {
        $this->parseResult = $parseResult;

        return $this;
    }

    /**
     * @return ParseResult
     */
    public function getParseResult()
    {
        return $this->parseResult ?: new ParseResult('', '', '');
    }
}
