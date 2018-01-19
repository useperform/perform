<?php

namespace Perform\MediaBundle\Storage;

use Perform\MediaBundle\Url\FileUrlGeneratorInterface;
use League\Flysystem\FilesystemInterface;
use League\Flysystem\FileNotFoundException;
use Perform\MediaBundle\Entity\File;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class Bucket
{
    protected $storage;
    protected $urlGenerator;
    protected $mediaTypes = [];

    public function __construct(FilesystemInterface $storage, FileUrlGeneratorInterface $urlGenerator, array $mediaTypes = [])
    {
        $this->storage = $storage;
        $this->urlGenerator = $urlGenerator;
        $this->mediaTypes = $mediaTypes;
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
