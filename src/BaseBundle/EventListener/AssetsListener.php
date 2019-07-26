<?php

namespace Perform\BaseBundle\EventListener;

use Perform\BaseBundle\Asset\Dumper\JavascriptTarget;
use Perform\BaseBundle\Event\AssetDumpEvent;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class AssetsListener
{
    const BUNDLE_DIR = __DIR__.'/..';

    protected $pathFile;
    protected $namespaces = [];
    protected $entrypoints = [];
    protected $extraSass = [];
    protected $jsModules = [];
    protected $theme;

    public function __construct($pathFile, array $namespaces, array $entrypoints, array $extraSass, array $jsModules, $theme)
    {
        $this->pathFile = $pathFile;
        $this->namespaces = $namespaces;
        $this->entrypoints = $entrypoints;
        $this->extraSass = $extraSass;
        $this->jsModules = $jsModules;
        $this->theme = trim($theme, '/');
    }

    public function onAddAssets(AssetDumpEvent $event)
    {
        $event->addTarget(new JavascriptTarget(self::BUNDLE_DIR.'/Resources/src/modules.js', $this->jsModules));
    }
}
