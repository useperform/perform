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
}
