<?php

namespace Perform\MediaBundle\MediaType;

use Perform\MediaBundle\Entity\File;
use Perform\MediaBundle\MediaResource;
use Perform\MediaBundle\Bucket\BucketInterface;
use Perform\MediaBundle\Event\ImportUrlEvent;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class YoutubeType implements MediaTypeInterface
{
    public static function getName()
    {
        return 'youtube';
    }

    public function supports(MediaResource $resource)
    {
        if ($resource->isFile() || !preg_match("`^youtube:(\w+)$`", $resource->getPath(), $matches)) {
            return false;
        }
        $resource->setPath($matches[1]);

        return true;
    }

    public function onUrlImport(ImportUrlEvent $event)
    {
        $urlRegex = "`(https?://)?(www.)?(youtube.com/watch\?v=|youtu.be/)(?P<id>\w+)`";
        // check for false positives, like /favicon
        if (!preg_match($urlRegex, $event->getUrl(), $matches)) {
            return;
        }
        $youtubeId = $matches['id'];
        // fetch the title from the page
        $name = 'Youtube video '.$youtubeId;
        $resource = new MediaResource('youtube:'.$youtubeId, $name, $event->getOwner());

        $event->addResource($resource);
    }

    public function process(File $file, MediaResource $resource, BucketInterface $bucket)
    {
    }

    public function getSuitableLocation(File $file, array $criteria)
    {
        if (isset($criteria['width']) && count($file->getExtraLocations()) > 0) {
            return $file->getExtraLocations()[0];
        }

        return $file->getPrimaryLocation();
    }
}
