<?php

namespace Admin\MediaBundle\Event;

use Admin\MediaBundle\Entity\File;
use Symfony\Component\EventDispatcher\Event;

/**
 * FileEvent.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class FileEvent extends Event
{
    //called when adding a new file
    const CREATE = 'admin_media.file.create';

    //called when a file needs processing
    const PROCESS = 'admin_media.file.process';

    //called when a file is deleted
    const DELETE = 'admin_media.file.delete';

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
