<?php

namespace Perform\MediaBundle\Plugin;

use Perform\MediaBundle\Entity\File;
use Perform\MediaBundle\Url\FileUrlGeneratorInterface;
use League\Flysystem\FilesystemInterface;

/**
 * PdfPlugin
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class PdfPlugin implements FilePluginInterface
{
    protected $type = 'pdf';
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
        return 'PDF';
    }

    public function getPreview(File $file, array $options = [])
    {
        return '<i class="fa fa-file-pdf-o"></i>';
    }

    public function onCreate(File $file)
    {
        if ($file->getMimeType() === 'application/pdf') {
            $file->setType($this->type);
        }
    }

    public function onProcess(File $file)
    {
        if ($file->getType() === $this->type) {
            $this->createThumbnail($file);
        }
    }

    public function onDelete(File $file)
    {
        //remove generated thumbnails
    }

    /**
     * Create a thumbnail of a PDF document.
     *
     * @param File $file
     */
    protected function createThumbnail(File $file)
    {
    }
}
