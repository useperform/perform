<?php

namespace Perform\MediaBundle\Plugin;

use Perform\MediaBundle\Entity\File;
use Perform\MediaBundle\Url\FileUrlGeneratorInterface;
use League\Flysystem\FilesystemInterface;

/**
 * AudioPlugin
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class AudioPlugin implements FilePluginInterface
{
    protected $type = 'audio';
    protected $storage;

    public function __construct(FilesystemInterface $storage)
    {
        $this->storage = $storage;
    }

    public function getName()
    {
        return $this->type;
    }

    public function getListingName()
    {
        return 'Audio';
    }

    public function getPreview(File $file, array $options = [])
    {
        return '<i class="fa fa-file-audio-o"></i>';
    }

    public function onCreate(File $file)
    {
        if (substr($file->getMimeType(), 0, 6) !== 'audio/') {
            return;
        }
        //mp3 support only just now
        if ($file->getMimeType() !== 'audio/mpeg') {
            return;
        }

        $file->setType($this->type);
    }

    public function onProcess(File $file)
    {
    }

    public function onDelete(File $file)
    {
    }
}
