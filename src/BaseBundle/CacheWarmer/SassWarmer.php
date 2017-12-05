<?php

namespace Perform\BaseBundle\CacheWarmer;

use Symfony\Component\HttpKernel\CacheWarmer\CacheWarmerInterface;
use Perform\BaseBundle\Util\BundleSearcher;
use Perform\BaseBundle\Asset\ThemeNotFoundException;
use Symfony\Component\Filesystem\Filesystem;
use Perform\BaseBundle\Asset\AssetNotFoundException;

/**
 * Find additional files to be included in scss compilation, such as
 * the theme and stylesheets from other bundles.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class SassWarmer implements CacheWarmerInterface
{
    protected $bundleSearcher;
    protected $fs;
    protected $theme;
    protected $extraFiles = [];

    const TARGET_DIR = __DIR__.'/../Resources/scss/';

    public function __construct(BundleSearcher $bundleSearcher, Filesystem $fs, $theme, array $extraFiles)
    {
        $this->bundleSearcher = $bundleSearcher;
        $this->theme = $theme;
        $this->fs = $fs;
        $this->extraFiles = $extraFiles;
    }

    public function warmUp($cacheDir)
    {
        $this->loadTheme();
        $this->loadExtraSass();
    }

    public function loadTheme()
    {
        list($themeBundle, $themeName) = $this->parseAsset($this->theme);
        $themeFiles = [
            'theme.scss' => '_theme.scss',
            'variables.scss' => '_theme_variables.scss',
        ];
        foreach ($themeFiles as $source => $target) {
            $relativePath = sprintf('scss/themes/%s/%s', $themeName, $source);

            try {
                $files = array_values(iterator_to_array($this->bundleSearcher->findResourcesAtPath($relativePath, [$themeBundle])));
            } catch (\Exception $e) {
                throw ThemeNotFoundException::missing($this->theme);
            }

            if (count($files) < 1) {
                throw ThemeNotFoundException::missingFile($this->theme, $source);
            }

            $this->dumpSassImport($target, [$files[0]->getPathname()]);
        }
    }

    public function loadExtraSass()
    {
        $paths = [];
        foreach ($this->extraFiles as $asset) {
            list($bundle, $name) = $this->parseAsset($asset);
            try {
                $relativePath = sprintf('scss/%s', $name);

                $files = array_values(iterator_to_array($this->bundleSearcher->findResourcesAtPath($relativePath, [$bundle])));
            } catch (\Exception $e) {
                throw AssetNotFoundException::missing($asset);
            }
            if (count($files) < 1) {
                throw AssetNotFoundException::missing($asset);
            }
            $paths[] = $files[0]->getPathname();
        }

        $this->dumpSassImport('_extras.scss', $paths);
    }

    private function parseAsset($name)
    {
        $pieces = explode(':', $name);
        if (count($pieces) !== 2) {
            throw AssetNotFoundException::invalid($name);
        }

        return $pieces;
    }

    private function dumpSassImport($target, array $paths)
    {
        $content = '';
        foreach ($paths as $path) {
            $importPath = rtrim($this->fs->makePathRelative($path, self::TARGET_DIR), '/');
            $content = sprintf('@import "%s";'.PHP_EOL, $importPath);
        }

        $this->fs->dumpFile(self::TARGET_DIR.$target, $content);
    }

    public function isOptional()
    {
        return false;
    }
}
