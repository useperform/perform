<?php

namespace Perform\MediaBundle\Bucket;

use Symfony\Component\DependencyInjection\ServiceLocator;
use Perform\MediaBundle\Entity\File;
use Perform\MediaBundle\Exception\BucketNotFoundException;

/**
 * A store of the available media buckets.
 * Each bucket will only be instantiated on first access.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class BucketRegistry implements BucketRegistryInterface
{
    protected $locator;
    protected $defaultBucket;

    public function __construct(ServiceLocator $locator, $defaultBucket)
    {
        $this->locator = $locator;
        $this->defaultBucket = $defaultBucket;
    }

    public function get($bucketName)
    {
        if (!$this->locator->has($bucketName)) {
            throw new BucketNotFoundException(sprintf('Media bucket "%s" was not found.', $bucketName));
        }

        return $this->locator->get($bucketName);
    }

    public function getDefault()
    {
        return $this->get($this->defaultBucket);
    }

    public function getForFile(File $file)
    {
        return $this->get($file->getBucketName());
    }
}
