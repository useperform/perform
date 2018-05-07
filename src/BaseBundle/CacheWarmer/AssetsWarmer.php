<?php

namespace Perform\BaseBundle\CacheWarmer;

use Symfony\Component\HttpKernel\CacheWarmer\CacheWarmerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Perform\BaseBundle\Event\AssetDumpEvent;
use Perform\BaseBundle\Asset\Dumper\Dumper;

/**
 * Dumps various files needed for asset compilation.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class AssetsWarmer implements CacheWarmerInterface
{
    protected $dispatcher;
    protected $dumper;

    public function __construct(EventDispatcherInterface $dispatcher, Dumper $dumper)
    {
        $this->dispatcher = $dispatcher;
        $this->dumper = $dumper;
    }

    public function warmUp($cacheDir)
    {
        $event = new AssetDumpEvent();
        $this->dispatcher->dispatch(AssetDumpEvent::ADD, $event);
        $this->dispatcher->dispatch(AssetDumpEvent::REMOVE, $event);

        foreach ($event->getTargets() as $target) {
            $this->dumper->dump($target);
        }
    }

    public function isOptional()
    {
        return false;
    }
}
