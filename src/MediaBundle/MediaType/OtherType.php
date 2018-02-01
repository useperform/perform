<?php

namespace Perform\MediaBundle\MediaType;

use Perform\MediaBundle\Entity\File;
use Perform\MediaBundle\MediaResource;
use Perform\MediaBundle\Bucket\BucketInterface;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class OtherType implements MediaTypeInterface
{
    public function supports(File $file, MediaResource $resource)
    {
        return $resource->isFile();
    }

    public function process(File $file, MediaResource $resource, BucketInterface $bucket)
    {
    }

    public function getSuitableLocation(File $file, array $criteria)
    {
        return $file->getLocation();
    }
}
