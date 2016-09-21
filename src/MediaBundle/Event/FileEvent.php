<?php

namespace Perform\MediaBundle\Event;

use Perform\MediaBundle\Entity\File;
use Symfony\Component\EventDispatcher\Event;

/**
 * FileEvent.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class FileEvent extends Event
{
    //called when adding a new file
    const CREATE = 'perform_media.file.create';

    //called when a file needs processing
    const PROCESS = 'perform_media.file.process';

    //called when a file is deleted
    const DELETE = 'perform_media.file.delete';

    protected $file;

    public function __construct(File $file)
    {
        $this->file = $file;
    }

    public function getFile()
    {
        return $this->file;
    }
}
