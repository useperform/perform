<?php

namespace Perform\BaseBundle\CacheWarmer;

use Symfony\Component\HttpKernel\CacheWarmer\CacheWarmerInterface;
use Symfony\Component\Filesystem\Filesystem;
use Perform\BaseBundle\Asset\Dumper\PathDumper;

/**
 * Dumps various files needed for asset compilation.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class AssetsWarmer implements CacheWarmerInterface
{
    protected $fs;
    protected $pathDumper;
    protected $pathFile;
    protected $javascriptModules = [];

    const BUNDLE_DIR = __DIR__.'/..';

    public function __construct(PathDumper $pathDumper, $pathFile, array $javascriptModules)
    {
        $this->fs = new Filesystem();
        $this->pathDumper = $pathDumper;
        $this->pathFile = $pathFile;
        $this->javascriptModules = $javascriptModules;
    }

    public function warmUp($cacheDir)
    {
        $this->pathDumper->dump($this->pathFile);
        $this->dumpJavascriptModules();
    }

    /**
     * Dump modules.js, added to the window.Perform object.
     */
    public function dumpJavascriptModules()
    {
        $content = '';
        foreach ($this->javascriptModules as $name => $import) {
            $content .= sprintf("import %s from '%s';".PHP_EOL, $name, $import);
        }
        $content .= 'export default {'.PHP_EOL;
        foreach (array_keys($this->javascriptModules) as $name) {
            $content .= sprintf('%s,'.PHP_EOL, $name);
        }
        $content .= '}';

        $this->fs->dumpFile(self::BUNDLE_DIR.'/Resources/src/modules.js', $content);
    }

    public function isOptional()
    {
        return false;
    }
}
