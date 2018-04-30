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
    protected $entrypoints = [];
    protected $javascriptModules = [];

    const BUNDLE_DIR = __DIR__.'/..';

    public function __construct(Filesystem $fs, array $namespaces, array $entrypoints, array $javascriptModules)
    {
        $this->fs = $fs;
        $this->namespaces = $namespaces;
        $this->entrypoints = $entrypoints;
        $this->javascriptModules = $javascriptModules;
    }

    public function warmUp($cacheDir)
    {
        $this->dumpWebpackConfig();
        $this->dumpJavascriptModules();
    }

    /**
     * Dump webpack-paths.js, used by webpack for resolve.alias and entrypoints
     */
    public function dumpWebpackConfig()
    {
        $data = [
            'entry' => [],
            'alias' => [],
        ];
        foreach ($this->entrypoints as $name => $entry) {
            $data['entry'][$name] = $entry;
        }
        foreach ($this->namespaces as $name => $path) {
            $data['alias'][$name] = rtrim($path, '/').'/';
        }
        $content = sprintf('module.exports = %s', json_encode($data));

        $this->fs->dumpFile(self::BUNDLE_DIR.'/webpack-paths.js', $content);
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
