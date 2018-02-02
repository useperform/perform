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
        $thumbnailData = [];

        foreach ($this->thumbnailWidths as $width) {
            if ($box->getWidth() < $width) {
                continue;
            }
            $thumbnailStream = fopen('php://temp', 'r+');
            $thumbnailImage = $image->copy();
            fwrite($thumbnailStream, $thumbnailImage->resize($box->widen($width))->get($this->getSaveFormat($file->getMimeType())));
            rewind($thumbnailStream);
            unset($thumbnailImage);

            $thumbnailLocation = Location::file(sprintf('thumbs/%s/%s.%s', $width, sha1($file->getId()), $this->getSaveFormat($file->getMimeType())));
            $thumbnailData[] = ['width' => $width, 'path' => $thumbnailLocation->getPath()];
            $bucket->save($thumbnailLocation, $thumbnailStream);
        }

        $file->setTypeOptions([
            'width' => $box->getWidth(),
            'thumbnails' => $thumbnailData,
        ]);
    }

    public function getSuitableLocation(File $file, array $criteria)
    {
        $defaultOptions = [
            'width' => INF,
            'thumbnails' => [],
        ];
        $typeOptions = array_merge($defaultOptions, $file->getTypeOptions());
        if (!isset($criteria['width'])) {
            return $file->getLocation();
        }

        $closest = null;
        foreach ($typeOptions['thumbnails'] as $thumbnail) {
            if (!isset($thumbnail['width'])) {
                continue;
            }

            if (!$closest || abs($criteria['width'] - $thumbnail['width']) < abs($closest - $criteria['width'])) {
                $closest = $thumbnail['width'];
            }
        }

        if (!$closest) {
            return $file->getLocation();
        }

        return Location::file(sprintf('thumbs/%s/%s.%s', $closest, sha1($file->getId()), $this->getSaveFormat($file->getMimeType())));
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
