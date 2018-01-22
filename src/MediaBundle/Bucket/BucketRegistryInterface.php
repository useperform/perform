<?php

namespace Perform\MediaBundle\Bucket;

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
     * @return string
     */
    public function getDefaultName();
}
