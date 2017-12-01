<?php

namespace Perform\BaseBundle\CacheWarmer;

use Symfony\Component\HttpKernel\CacheWarmer\CacheWarmerInterface;
use Perform\BaseBundle\Util\BundleSearcher;
use Perform\BaseBundle\Asset\ThemeNotFoundException;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Find additional files to be included in scss compilation, such as
 * the theme and stylesheets from other bundles.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class SassWarmer implements CacheWarmerInterface
{
    protected $bundleSearcher;
    protected $theme;
    protected $fs;

    const TARGET_DIR = __DIR__.'/../Resources/scss/';

    public function __construct(BundleSearcher $bundleSearcher, Filesystem $fs, $theme)
    {
        $this->bundleSearcher = $bundleSearcher;
        $this->theme = $theme;
        $this->fs = $fs;
    }

    public function warmUp($cacheDir)
    {
        $pieces = explode(':', $this->theme);
        $themeBundle = $pieces[0];
        $themeName = $pieces[1];
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

            $importPath = rtrim($this->fs->makePathRelative($files[0]->getPathname(), self::TARGET_DIR), '/');
            $content = sprintf('@import "%s";', $importPath);
            $this->fs->dumpFile(self::TARGET_DIR.$target, $content);
        }
    }

    public function isOptional()
    {
        return false;
    }
}
