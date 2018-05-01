<?php

namespace Perform\BaseBundle\CacheWarmer;

use Symfony\Component\HttpKernel\CacheWarmer\CacheWarmerInterface;
use Symfony\Component\Filesystem\Filesystem;
use Perform\BaseBundle\Asset\Dumper\PathDumper;
use Perform\BaseBundle\Asset\Dumper\JavascriptDumper;
use Perform\BaseBundle\Asset\Dumper\SassDumper;

/**
 * Dumps various files needed for asset compilation.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class AssetsWarmer implements CacheWarmerInterface
{
    protected $sassDumper;
    protected $jsDumper;
    protected $pathDumper;
    protected $pathFile;

    const BUNDLE_DIR = __DIR__.'/..';

    public function __construct(SassDumper $sassDumper, JavascriptDumper $jsDumper, PathDumper $pathDumper, $pathFile)
    {
        $this->sassDumper = $sassDumper;
        $this->jsDumper = $jsDumper;
        $this->pathDumper = $pathDumper;
        $this->pathFile = $pathFile;
    }

    public function warmUp($cacheDir)
    {
        $this->sassDumper->dumpExtras(self::BUNDLE_DIR.'/Resources/scss/_extras.scss');
        $this->sassDumper->dumpTheme(self::BUNDLE_DIR.'/Resources/scss/_theme.scss');
        $this->sassDumper->dumpThemeVariables(self::BUNDLE_DIR.'/Resources/scss/_theme_variables.scss');
        $this->jsDumper->dump(self::BUNDLE_DIR.'/Resources/src/modules.js');
        $this->pathDumper->dump($this->pathFile);
    }

    public function isOptional()
    {
        return false;
    }
}
