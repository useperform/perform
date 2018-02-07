<?php

namespace Perform\MediaBundle\Bucket;

use Perform\MediaBundle\Entity\Location;
use Perform\MediaBundle\Url\UrlGeneratorInterface;
use Perform\MediaBundle\Entity\File;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
interface BucketInterface
{
    /**
     * @return string
     */
    public function getName();

    /**
     * @return MediaTypeInterface
     */
    public function getMediaType($name);

    /**
     * @return MediaTypeInterface[]
     */
    public function getMediaTypes();

    /**
     * Get the minimum size of files allowed in this bucket.
     *
     * @return int The minimum size in bytes
     */
    public function getMinSize();

    /**
     * Get the maximum size of files allowed in this bucket.
     *
     * @return int The maximum size in bytes
     */
    public function getMaxSize();

    /**
     * Save a file to storage.
     *
     * If the supplied location is a URL, don't do anything.
     */
    public function save(Location $location, $dataStream);

    /**
     * Read a file as a stream.
     *
     * If the supplied location is a URL, return null.
     *
     * @return resource
     */
    public function read(Location $location);

    /**
     * Check if a file exists in storage.
     *
     * If the supplied location is a URL, return false.
     */
    public function has(Location $location);

    /**
     * Delete a record location from storage.
     *
     * If the supplied location is a URL, don't do anything.
     */
    public function delete(Location $location);

    /**
     * Delete a file and all extra locations from storage.
     */
    public function deleteFile(File $file);

    /**
     * @return UrlGeneratorInterface
     */
    public function getUrlGenerator();
}
