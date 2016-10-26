<?php

namespace Perform\MediaBundle\Plugin;

use Perform\MediaBundle\Entity\File;
use Perform\MediaBundle\Url\FileUrlGeneratorInterface;
use League\Flysystem\FilesystemInterface;
use Imagine\Image\ImagineInterface;
use League\Flysystem\FileNotFoundException;

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
    protected $imagine;

    public function __construct(FilesystemInterface $storage, FileUrlGeneratorInterface $urlGenerator, ImagineInterface $imagine)
    {
        $this->storage = $storage;
        $this->urlGenerator = $urlGenerator;
        $this->imagine = $imagine;
    }

    public function getName()
    {
        return $this->type;
    }

    public function getListingName()
    {
        return 'Image';
    }

    public function getPreview(File $file, array $options = [])
    {
        $url = $this->urlGenerator->getUrl($file->getFilename());
        $typeOptions = $file->getTypeOptions();

        if (isset($options['size']) && isset($typeOptions['thumbnails'][$options['size']])) {
            $thumbUrl = $this->urlGenerator->getUrl($typeOptions['thumbnails'][$options['size']]);
        } else {
            $thumbUrl = $url;
        }

        return sprintf('<img class="img-responsive" src="%s" ref="%s"/>', $thumbUrl, $url);
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
        if ($file->getType() !== $this->type) {
            return;
        }

        $this->createThumbnail($file);
    }

    public function onDelete(File $file)
    {
        try {
            $this->storage->delete('thumbs/'.$file->getFilename());
        } catch (FileNotFoundException $e) {
        }
    }

    /**
     * Create and save a thumbnail of an image, storing a reference in the file
     * type options.
     *
     * @param File $file
     */
    public function createThumbnail(File $file)
    {
        $image = $this->imagine->load($this->storage->read($file->getFilename()));
        $box = $image->getSize()->widen(200);

        $thumbFilename = 'thumbs/'.$file->getFilename();
        $thumbData = $image->resize($box)->get($this->getSaveFormat($file->getMimeType()));
        $this->storage->write($thumbFilename, $thumbData);

        $file->setTypeOptions([
            'thumbnails' => [
                'small' => $thumbFilename,
            ],
        ]);
    }

    protected function getSaveFormat($mime_type)
    {
        $pieces = explode('/', $mime_type);
        $availableTypes = ['gif', 'png', 'wbmp', 'xbm'];
        if (isset($pieces[1]) && in_array($pieces[1], $availableTypes)) {
            return $pieces[1];
        }

        return 'jpeg';
    }
}
