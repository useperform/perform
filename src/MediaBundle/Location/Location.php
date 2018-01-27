<?php

namespace Perform\MediaBundle\Location;

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
     * @var string
     */
    protected $path;

    const TYPE_FILE = 0;
    const TYPE_URL = 1;

    /**
     * @var int
     */
    protected $type;

    /**
     * @param string $path
     * @param int $type
     */
    public function __construct($path, $type)
    {
        $this->path = $path;
        $this->type = (int) $type;
    }

    /**
     * @param string $file
     */
    public static function file($path)
    {
        return new self($path, self::TYPE_FILE);
    }

    /**
     * @param string $url
     */
    public static function url($url)
    {
        return new self($url, self::TYPE_URL);
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @return int
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return bool
     */
    public function isFile()
    {
        return $this->type === self::TYPE_FILE;
    }

    /**
     * @return bool
     */
    public function isUrl()
    {
        return $this->type === self::TYPE_URL;
    }
}
