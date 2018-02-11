<?php

namespace Perform\MediaBundle\MediaType;

use Perform\MediaBundle\Entity\File;
use Imagine\Image\ImagineInterface;
use Perform\MediaBundle\MediaResource;
use Perform\MediaBundle\Bucket\BucketInterface;
use Perform\MediaBundle\Entity\Location;

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

    public static function getName()
    {
        return 'image';
    }

    public function supports(File $file, MediaResource $resource)
    {
        if (!$resource->isFile()) {
            return false;
        }

        $mimeType = $file->getPrimaryLocation()->getMimeType();
        if (substr($mimeType, 0, 6) !== 'image/') {
            return false;
        }
        //no support for icon files for now - GD blows up
        return $mimeType !== 'image/x-icon';
    }

    public function process(File $file, MediaResource $resource, BucketInterface $bucket)
    {
        $image = $this->imagine->read(fopen($resource->getPath(), 'r'));
        $box = $image->getSize();
        $location = $file->getPrimaryLocation();
        $location->setAttribute('width', $box->getWidth());
        $location->setAttribute('height', $box->getHeight());

        foreach ($this->thumbnailWidths as $width) {
            if ($box->getWidth() < $width) {
                continue;
            }
            $thumbnailStream = fopen('php://temp', 'r+');
            $thumbnailImage = $image->copy();
            $format = $this->getSaveFormat($file->getPrimaryLocation()->getMimeType());
            fwrite($thumbnailStream, $thumbnailImage->resize($box->widen($width))->get($format));
            rewind($thumbnailStream);

            $thumbnailLocation = Location::file(
                sprintf('thumbs/%s/%s.%s', $width, sha1($file->getId()), $format),
                [
                    'width' => $width,
                    'height' => $thumbnailImage->getSize()->getHeight(),
                ]
            );
            $thumbnailLocation->setMimeType('image/'.$format);
            $thumbnailLocation->setCharset('binary');
            unset($thumbnailImage);
            $bucket->save($thumbnailLocation, $thumbnailStream);
            $file->addLocation($thumbnailLocation);
        }
    }

    public function getSuitableLocation(File $file, array $criteria)
    {
        $location = $file->getPrimaryLocation();
        if (!isset($criteria['width'])) {
            return $location;
        }

        $closestDifference = INF;
        $bestMatch = $location;
        foreach ($file->getExtraLocations() as $thumbnail) {
            $thumbnailWidth = $thumbnail->getAttribute('width');
            if (!$thumbnailWidth) {
                continue;
            }

            $difference = $thumbnailWidth - $criteria['width'];
            if (($difference >= 0 && $difference < $closestDifference)) {
                $closestDifference = $difference;
                $bestMatch = $thumbnail;
            }
        }

        return $bestMatch;
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
