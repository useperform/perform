<?php

namespace Perform\MediaBundle\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Perform\MediaBundle\Event\FileEvent;
use Perform\MediaBundle\Plugin\PluginRegistry;

/**
 * FilePluginListener
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class FilePluginListener implements EventSubscriberInterface
{
    public function __construct(PluginRegistry $registry)
    {
        $this->registry = $registry;
    }

    public function onFileCreate(FileEvent $event)
    {
        return $this->registry->onFileCreate($event->getFile());
    }

    public function onFileProcess(FileEvent $event)
    {
        return $this->registry->onFileProcess($event->getFile());
    }

    public function onFileDelete(FileEvent $event)
    {
        return $this->registry->onFileDelete($event->getFile());
    }

    public static function getSubscribedEvents()
    {
        return [
            FileEvent::CREATE => ['onFileCreate'],
            FileEvent::PROCESS => ['onFileProcess'],
            FileEvent::DELETE => ['onFileDelete'],
        ];
    }
}
