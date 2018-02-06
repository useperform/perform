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
        $file->setLocationAttribute('width', $box->getWidth());
        $file->setLocationAttribute('height', $box->getHeight());

        foreach ($this->thumbnailWidths as $width) {
            if ($box->getWidth() < $width) {
                continue;
            }
            $thumbnailStream = fopen('php://temp', 'r+');
            $thumbnailImage = $image->copy();
            fwrite($thumbnailStream, $thumbnailImage->resize($box->widen($width))->get($this->getSaveFormat($file->getMimeType())));
            rewind($thumbnailStream);

            $thumbnailLocation = Location::file(
                sprintf('thumbs/%s/%s.%s', $width, sha1($file->getId()), $this->getSaveFormat($file->getMimeType())),
                [
                    'width' => $width,
                    'height' => $thumbnailImage->getSize()->getHeight(),
                ]
            );
            unset($thumbnailImage);
            $bucket->save($thumbnailLocation, $thumbnailStream);
            $file->addExtraLocation($thumbnailLocation);
        }
    }

    public function getSuitableLocation(File $file, array $criteria)
    {
        $location = $file->getLocation();
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
