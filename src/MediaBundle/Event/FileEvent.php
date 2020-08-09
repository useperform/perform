<?php

namespace Perform\MediaBundle\Event;

use Perform\MediaBundle\Entity\File;
use Perform\MediaBundle\MediaResource;
use Symfony\Component\EventDispatcher\Event;

class FileEvent extends Event
{
    //called when adding a new file
    const CREATE = 'perform_media.file.create';

    //called when a file needs processing
    const PROCESS = 'perform_media.file.process';

    //called when a file is deleted
    const DELETE = 'perform_media.file.delete';

    private $file;
    private $resource;

    public function __construct(File $file, MediaResource $resource = null)
    {
        $this->file = $file;
        $this->resource = $resource;
    }

    public function getFile()
    {
        return $this->file;
    }

    public function getResource()
    {
        return $this->resource;
    }
}
