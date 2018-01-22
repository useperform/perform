<?php

namespace Perform\MediaBundle\Bucket;

use Perform\MediaBundle\Url\FileUrlGeneratorInterface;
use League\Flysystem\FilesystemInterface;
use League\Flysystem\FileNotFoundException;
use Perform\MediaBundle\Entity\File;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class Bucket implements BucketInterface
{
    protected $storage;
    protected $urlGenerator;
    protected $mediaTypes = [];

    public function __construct(FilesystemInterface $storage, FileUrlGeneratorInterface $urlGenerator, array $mediaTypes = [])
    {
        $this->storage = $storage;
        $this->urlGenerator = $urlGenerator;
        // $this->pluginRegistry = $pluginRegistry;
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

    public function writeStream(File $file, $dataStream)
    {
        $this->storage->writeStream($file->getFilename(), $dataStream);
    }

    public function has(File $file)
    {
        return $this->storage->has($file->getFilename());
    }

    public function delete(File $file)
    {
        try {
            $this->storage->delete($file->getFilename());
        } catch (FileNotFoundException $e) {
            //already deleted
        }
    }
}
