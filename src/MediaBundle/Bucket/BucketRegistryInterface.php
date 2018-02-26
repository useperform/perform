<?php

namespace Perform\MediaBundle\Bucket;

use Perform\MediaBundle\Entity\File;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
interface BucketRegistryInterface
{
    /**
     * @return BucketInterface
     */
    public function get($bucketName);

    /**
     * @return BucketInterface
     */
    public function getDefault();

    /**
     * @return BucketInterface
     */
    public function getForFile(File $file);
}
