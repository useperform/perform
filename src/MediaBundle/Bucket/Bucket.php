<?php

namespace Perform\MediaBundle\Bucket;

use Perform\MediaBundle\Url\UrlGeneratorInterface;
use League\Flysystem\FilesystemInterface;
use League\Flysystem\FileNotFoundException;
use Perform\MediaBundle\Entity\File;
use Perform\MediaBundle\Entity\Location;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class Bucket implements BucketInterface
{
    /**
     * @var string
     */
    protected $name;
    protected $storage;
    protected $urlGenerator;
    protected $mediaTypes = [];

    public function __construct($name, FilesystemInterface $storage, UrlGeneratorInterface $urlGenerator, array $mediaTypes = [])
    {
        $this->name = $name;
        $this->storage = $storage;
        $this->urlGenerator = $urlGenerator;
        $this->mediaTypes = $mediaTypes;
    }

    public function getUrlGenerator()
    {
        return $this->urlGenerator;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    public function getMinSize()
    {
        return 0;
    }

    public function getMaxSize()
    {
        return INF;
    }

    public function getMediaTypes()
    {
        return $this->mediaTypes;
    }

    public function save(Location $location, $dataStream)
    {
        if ($location->isFile() && !$this->storage->has($location->getPath())) {
            $this->storage->writeStream($location->getPath(), $dataStream);
        }
    }

    public function read(Location $location)
    {
        if ($location->isFile()) {
            return $this->storage->readStream($location->getPath());
        }
    }

    public function has(Location $location)
    {
        return $location->isFile() && $this->storage->has($location->getPath());
    }

    public function delete(Location $location)
    {
        try {
            if (!$location->isFile()) {
                return;
            }

            $this->storage->delete($location->getPath());
        } catch (FileNotFoundException $e) {
            //already deleted
        }
    }

    /**
     * Delete all locations linked to a media item from storage.
     */
    public function deleteFile(File $file)
    {
        $this->delete($file->getLocation());
        foreach ($file->getExtraLocations() as $location) {
            $this->delete($location);
        }
    }
}
