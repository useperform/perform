<?php

namespace Perform\MediaBundle\MediaType;

use Perform\MediaBundle\Entity\File;
use Perform\MediaBundle\Bucket\BucketInterface;
use Perform\MediaBundle\MediaResource;
use Perform\MediaBundle\Entity\Location;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
interface MediaTypeInterface
{
    /**
     * @return string
     */
    public static function getName();

    /**
     * @return bool
     */
    public function supports(File $file, MediaResource $resource);

    /**
     * @param File $file
     * @param MediaResource $resource
     * @param BucketInterface $bucket
     */
    public function process(File $file, MediaResource $resource, BucketInterface $bucket);

    /**
     * Get a Location object for a file that matches the given criteria.
     *
     * For example, if the criteria specifies files that are a
     * particular size, the media type should return a file closest to
     * that size.
     *
     * @return Location
     */
    public function getSuitableLocation(File $file, array $criteria);
}
