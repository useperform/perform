<?php

namespace Admin\MediaBundle\Plugin;

use Admin\MediaBundle\Entity\File;
use Admin\MediaBundle\Url\FileUrlGeneratorInterface;
use League\Flysystem\FilesystemInterface;

/**
 * ImagePlugin.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class ImagePlugin implements FilePluginInterface
{
    protected $type = 'image';
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
        return 'Image';
    }

    public function getUrl(File $file)
    {
        return $this->urlGenerator->getUrl($file);
    }

    public function getPreview(File $file, array $options = [])
    {
        $url = $this->urlGenerator->getUrl($file);
        //thumbUrl should find a linked private variation of the file and get the url for that.
        $thumbUrl = $url;

        return sprintf('<img src="%s" ref="%s"/>', $thumbUrl, $url);
    }

    public function onCreate(File $file)
    {
        if (substr($file->getMimeType(), 0, 6) !== 'image/') {
            return;
        }
        //no support for icon files for now - GD blows up
        if ($file->getMimeType() === 'image/x-icon') {
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
