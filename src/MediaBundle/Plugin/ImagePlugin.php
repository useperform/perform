<?php

namespace Admin\MediaBundle\Plugin;

use Admin\MediaBundle\Entity\File;
use Admin\MediaBundle\Url\FileUrlGeneratorInterface;
use League\Flysystem\FilesystemInterface;
use Imagine\Image\ImagineInterface;

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
        if ($file->getType() !== $this->type) {
            return;
        }

        $this->createThumbnail($file);
    }

    public function onDelete(File $file)
    {
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
        $this->storage->write($thumbFilename, $image->resize($box)->get($this->getSaveFormat($file->getMimeType())));

        $file->setTypeOptions([
            'thumbnails' => [
                'small' => $thumbFilename,
            ],
        ]);
    }

    protected function getSaveFormat($mime_type)
    {
        $type = explode('/', $mime_type)[1];
        if (in_array($type, ['gif', 'png', 'wbmp', 'xbm'])) {
            return $type;
        }

        // if all else fails, go with jpeg
        return 'jpeg';
    }
}
