<?php

namespace Perform\BaseBundle\CacheWarmer;

use Symfony\Component\HttpKernel\CacheWarmer\CacheWarmerInterface;
use Symfony\Component\Filesystem\Filesystem;
use Perform\BaseBundle\Asset\Dumper\PathDumper;
use Perform\BaseBundle\Asset\Dumper\JavascriptDumper;

/**
 * Dumps various files needed for asset compilation.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class AssetsWarmer implements CacheWarmerInterface
{
    protected $jsDumper;
    protected $pathDumper;
    protected $pathFile;

    const BUNDLE_DIR = __DIR__.'/..';

    public function __construct(JavascriptDumper $jsDumper, PathDumper $pathDumper, $pathFile)
    {
        $this->jsDumper = $jsDumper;
        $this->pathDumper = $pathDumper;
        $this->pathFile = $pathFile;
    }

    public function warmUp($cacheDir)
    {
        $this->jsDumper->dump(self::BUNDLE_DIR.'/Resources/src/modules.js');
        $this->pathDumper->dump($this->pathFile);
    }

    public function isOptional()
    {
        return false;
    }
}
