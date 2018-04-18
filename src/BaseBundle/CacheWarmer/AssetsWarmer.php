<?php

namespace Perform\BaseBundle\CacheWarmer;

use Symfony\Component\HttpKernel\CacheWarmer\CacheWarmerInterface;
use Perform\BaseBundle\Util\BundleSearcher;
use Perform\BaseBundle\Asset\ThemeNotFoundException;
use Symfony\Component\Filesystem\Filesystem;
use Perform\BaseBundle\Asset\AssetNotFoundException;

/**
 * Dumps various files needed by the webpack build.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class AssetsWarmer implements CacheWarmerInterface
{
    protected $fs;
    protected $namespaces = [];
    protected $javascriptModules = [];

    const BUNDLE_DIR = __DIR__.'/..';

    public function __construct(Filesystem $fs, array $namespaces, array $javascriptModules)
    {
        $this->fs = $fs;
        $this->namespaces = $namespaces;
        $this->javascriptModules = $javascriptModules;
    }

    public function warmUp($cacheDir)
    {
        $this->dumpNamespaces();
        $this->dumpJavascriptModules();
    }

    /**
     * Dump namespaces.js, used by webpack for resolve.alias
     */
    public function dumpNamespaces()
    {
        $content = 'module.exports = {';
        foreach ($this->namespaces as $name => $path) {
            $content .= sprintf("'%s': '%s',", $name, rtrim($path, '/').'/');
        }
        $content = rtrim($content, ',').'};';

        $this->fs->dumpFile(self::BUNDLE_DIR.'/namespaces.js', $content);
    }

    /**
     * Dump modules.js, added to the window.Perform object
     */
    public function dumpJavascriptModules()
    {
        $content = '';
        foreach ($this->javascriptModules as $name => $import) {
            $content .= sprintf("import %s from '%s';".PHP_EOL, $name, $import);
        }
        $content .= 'export default {'.PHP_EOL;
        foreach (array_keys($this->javascriptModules) as $name) {
            $content .= sprintf("%s,".PHP_EOL, $name);
        }
        $content .= '}';

        $this->fs->dumpFile(self::BUNDLE_DIR.'/Resources/src/modules.js', $content);
    }

    public function isOptional()
    {
        return false;
    }
}
