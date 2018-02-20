<?php

namespace Perform\MediaBundle\Entity;

/**
 * Represents a file in a bucket, or a resource accessible through a URL.
 *
 * This abstraction exists to allow for the same record to
 * reference both URLs (e.g. videos on a website) and files
 * (thumbnails for the videos).
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class Location
{
    /**
     * @var guid
     */
    protected $id;

    /**
     * @var string
     */
    protected $path;

    /**
     * @var bool
     */
    protected $isFile = false;

    /**
     * @var array
     */
    protected $attributes = [];

    /**
     * @var bool
     */
    protected $isPrimary = false;

    /**
     * @var File
     */
    protected $file;

    /**
     * @var string
     */
    protected $mimeType;

    /**
     * @var string
     */
    protected $charset;

    /**
     * @param string $path
     * @param array $attributes
     */
    public function __construct($path, array $attributes = [])
    {
        $this->path = $path;
        $this->attributes = $attributes;
    }

    /**
     * @param string $file
     */
    public static function file($path, array $attributes = [])
    {
        $location = new self($path, $attributes);
        $location->isFile = true;

        return $location;
    }

    /**
     * @return guid
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Mark this location as referencing a file in the bucket.
     *
     * @param bool $isFile
     *
     * @return static
     */
    // public function setFile($isFile)
    // {
    //     $this->isFile = (bool) $isFile;

    //     return $this;
    // }

    /**
     * Return true if this location is a file (and should be stored in
     * the bucket).
     *
     * @return bool
     */
    public function isFile()
    {
        return $this->isFile;
    }

    /**
     * @param array $attributes
     *
     * @return File
     */
    public function setAttributes(array $attributes)
    {
        $this->attributes = $attributes;

        return $this;
    }

    /**
     * @return array
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * @param string $name
     * @param mixed $value
     *
     * @return static
     */
    public function setAttribute($name, $value)
    {
        $this->attributes[$name] = $value;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getAttribute($name, $default = null)
    {
        return isset($this->attributes[$name]) ? $this->attributes[$name] : $default;
    }

    /**
     * @var bool $isPrimary
     */
    public function setPrimary($isPrimary)
    {
        $this->isPrimary = (bool) $isPrimary;

        return $this;
    }

    /**
     * @return bool
     */
    public function isPrimary()
    {
        return $this->isPrimary;
    }

    /**
     * @param File $file
     *
     * @return Location
     */
    public function setFile(File $file)
    {
        $this->file = $file;

        return $this;
    }

    /**
     * @return File
     */
    public function getFile()
    {
        return $this->file;
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

}
