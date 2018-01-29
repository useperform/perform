<?php

namespace Perform\MediaBundle\MediaType;

use Perform\MediaBundle\Entity\File;
use Imagine\Image\ImagineInterface;
use Perform\MediaBundle\MediaResource;
use Perform\MediaBundle\Bucket\BucketInterface;
use Perform\MediaBundle\Location\Location;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class ImageType implements MediaTypeInterface
{
    protected $imagine;
    protected $thumbnailWidths;

    public function __construct(ImagineInterface $imagine, array $thumbnailWidths = [])
    {
        $this->imagine = $imagine;
        $this->thumbnailWidths = $thumbnailWidths;
    }

    public function supports(File $file, MediaResource $resource)
    {
        if (!$resource->isFile()) {
            return false;
        }

        if (substr($file->getMimeType(), 0, 6) !== 'image/') {
            return false;
        }
        //no support for icon files for now - GD blows up
        return $file->getMimeType() !== 'image/x-icon';
    }

    public function process(File $file, MediaResource $resource, BucketInterface $bucket)
    {
        $image = $this->imagine->read(fopen($resource->getPath(), 'r'));
        $box = $image->getSize();

        foreach ($this->thumbnailWidths as $width) {
            if ($box->getWidth() < $width) {
                continue;
            }
            $thumbnailStream = fopen('php://temp', 'r+');
            fwrite($thumbnailStream, $image->resize($box->widen($width))->get($this->getSaveFormat($file->getMimeType())));
            rewind($thumbnailStream);

            $thumbnailLocation = Location::file(sprintf('thumbs/%s/%s.%s', $width, sha1($file->getId()), $this->getSaveFormat($file->getMimeType())));
            $file->setTypeOptions([
                'thumbnails' => [
                    ['w' => $width, 'path' => $thumbnailLocation->getPath()],
                ],
            ]);
            $bucket->save($thumbnailLocation, $thumbnailStream);
        }
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
