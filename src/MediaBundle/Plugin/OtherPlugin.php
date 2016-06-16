<?php

namespace Admin\MediaBundle\Plugin;

use Admin\MediaBundle\Entity\File;
use Admin\MediaBundle\Url\FileUrlGeneratorInterface;
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
    protected $urlGenerator;

    public function __construct(FilesystemInterface $storage, FileUrlGeneratorInterface $urlGenerator)
    {
        $this->storage = $storage;
        $this->urlGenerator = $urlGenerator;
    }

    public function getName()
    {
        return $this->type;
    }

    public function getListingName()
    {
        return 'Other';
    }

    public function getUrl(File $file)
    {
        return $this->urlGenerator->getUrl($file);
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
