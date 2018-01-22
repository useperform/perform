<?php

namespace Perform\MediaBundle\Bucket;

use Symfony\Component\DependencyInjection\ServiceLocator;
use Perform\MediaBundle\Entity\File;

/**
 * A store of the available media buckets.
 * Each bucket will only be instantiated on first access.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class BucketRegistry implements BucketRegistryInterface
{
    protected $locator;

    public function __construct(ServiceLocator $locator)
    {
        $this->locator = $locator;
    }

    public function getDefaultName()
    {
        return 'main';
    }

    public function get($bucketName)
    {
        if (!$this->locator->has($bucketName)) {
            throw new BucketNotFoundException(sprintf('Media bucket "%s" was not found.', $bucketName));
        }

        return $this->locator->get($bucketName);
    }

    public function getForFile(File $file)
    {
        return $this->get($file->getBucketName());
    }
}
