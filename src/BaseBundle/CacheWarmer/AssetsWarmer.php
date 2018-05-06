<?php

namespace Perform\BaseBundle\CacheWarmer;

use Symfony\Component\HttpKernel\CacheWarmer\CacheWarmerInterface;
use Perform\BaseBundle\Asset\Dumper\PathDumper;
use Perform\BaseBundle\Asset\Dumper\SassDumper;
use Perform\BaseBundle\Asset\Dumper\JavascriptDumper;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Perform\BaseBundle\Event\AssetDumpEvent;

/**
 * Dumps various files needed for asset compilation.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class AssetsWarmer implements CacheWarmerInterface
{
    protected $dispatcher;
    protected $pathDumper;
    protected $sassDumper;
    protected $jsDumper;

    public function __construct(EventDispatcherInterface $dispatcher, PathDumper $pathDumper, SassDumper $sassDumper, JavascriptDumper $jsDumper)
    {
        $this->dispatcher = $dispatcher;
        $this->pathDumper = $pathDumper;
        $this->sassDumper = $sassDumper;
        $this->jsDumper = $jsDumper;
    }

    public function warmUp($cacheDir)
    {
        $event = new AssetDumpEvent();
        $this->dispatcher->dispatch(AssetDumpEvent::ADD, $event);
        $this->dispatcher->dispatch(AssetDumpEvent::REMOVE, $event);

        foreach ($event->getPathTargets() as $target) {
            $this->pathDumper->dump($target);
        }
        foreach ($event->getSassTargets() as $target) {
            $this->sassDumper->dump($target);
        }
        foreach ($event->getJavascriptTargets() as $target) {
            $this->jsDumper->dump($target);
        }
    }

    public function isOptional()
    {
        return false;
    }
}
