<?php

namespace Perform\MediaBundle\MediaType;

use Perform\MediaBundle\Entity\File;
use Perform\MediaBundle\MediaResource;
use Perform\MediaBundle\Bucket\BucketInterface;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class AudioType implements MediaTypeInterface
{
    public function supports(File $file, MediaResource $resource)
    {
        if (!$resource->isFile()) {
            return false;
        }

        //mp3 support only just now
        return $file->getMimeType() == 'audio/mpeg';
    }

    public function process(File $file, MediaResource $resource, BucketInterface $bucket)
    {
    }

    public function getSuitableLocation(File $file, array $criteria)
    {
        return $file->getLocation();
    }
}
