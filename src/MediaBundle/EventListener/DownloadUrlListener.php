<?php

namespace Perform\MediaBundle\EventListener;

use Perform\MediaBundle\Event\ImportUrlEvent;
use Perform\MediaBundle\MediaResource;

/**
 * When importing a URL, download it as a file if nothing else has been done with it yet.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class DownloadUrlListener
{
    public function onImport(ImportUrlEvent $event)
    {
        if (!empty($event->getResources())) {
            return;
        }

        $local = tempnam(sys_get_temp_dir(), 'perform-media');
        $url = $event->getUrl();
        copy($url, $local);

        $name = $event->getName() ?: basename(parse_url($url, PHP_URL_PATH));
        $resource = new MediaResource($local, $name, $event->getOwner());
        $resource->deleteAfterProcess();

        $event->addResource($resource);
    }
}
