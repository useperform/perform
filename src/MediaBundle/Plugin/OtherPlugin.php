<?php

namespace Admin\MediaBundle\Plugin;

use Admin\MediaBundle\Entity\File;
use League\Flysystem\FilesystemInterface;

/**
 * OtherPlugin.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class OtherPlugin implements FilePluginInterface
{
    protected $type = 'other';
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
        return 'Other';
    }

    public function getPreview(File $file, array $options = [])
    {
        return '<i class="fa fa-file-o"></i>';
    }

    public function onCreate(File $file)
    {
        $file->setType($this->type);
    }

    public function onProcess(File $file)
    {
    }

    public function onDelete(File $file)
    {
    }
}
