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

    const BUNDLE_DIR = __DIR__.'/..';

    public function __construct(Filesystem $fs, array $namespaces)
    {
        $this->fs = $fs;
        $this->namespaces = $namespaces;
    }

    public function warmUp($cacheDir)
    {
        $this->dumpNamespaces();
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

    public function isOptional()
    {
        return false;
    }
}
