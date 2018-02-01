<?php

namespace Perform\MediaBundle\MediaType;

use Perform\MediaBundle\Entity\File;
use Perform\MediaBundle\Bucket\BucketInterface;
use Perform\MediaBundle\MediaResource;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
interface MediaTypeInterface
{
    /**
     * @return bool
     */
    public function supports(File $file, MediaResource $resource);

    public function process(File $file, MediaResource $pathname, BucketInterface $bucket);

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
