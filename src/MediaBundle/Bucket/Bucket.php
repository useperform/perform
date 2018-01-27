<?php

namespace Perform\MediaBundle\Bucket;

use Perform\MediaBundle\Url\FileUrlGeneratorInterface;
use League\Flysystem\FilesystemInterface;
use League\Flysystem\FileNotFoundException;
use Perform\MediaBundle\Entity\File;
use Perform\MediaBundle\Location\Location;

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

    public function __construct($name, FilesystemInterface $storage, FileUrlGeneratorInterface $urlGenerator, array $mediaTypes = [])
    {
        $this->name = $name;
        $this->storage = $storage;
        $this->urlGenerator = $urlGenerator;
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

    public function getUrl(File $file, array $criteria = [])
    {
        $plugin = $this->pluginRegistry->getPlugin($file->getPlugin());
        $path = $plugin->getMediaPath($file, $criteria);
        if ($path->isUrl()) {
            return $path->getPath();
        }

        return $this->urlGenerator->getUrl($path->getPath());
    }

    public function save(Location $location, $dataStream)
    {
        if ($location->isFile()) {
            $this->storage->writeStream($location->getPath(), $dataStream);
        }
    }

    public function has(Location $location)
    {
        return $location->isFile() && $this->storage->has($location->getLocation());
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
}
