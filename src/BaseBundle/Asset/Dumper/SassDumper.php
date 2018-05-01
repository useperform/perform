<?php

namespace Perform\BaseBundle\Asset\Dumper;

use Symfony\Component\Filesystem\Filesystem;

/**
 * Dump modules.js, added to the window.Perform object.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class SassDumper
{
    protected $fs;
    protected $theme;
    protected $extraFiles = [];

    public function __construct(Filesystem $fs, $theme, array $extraFiles)
    {
        $this->fs = $fs;
        $this->theme = $theme;
        $this->extraFiles = $extraFiles;
    }

    /**
     * @param string $filepath the path of the generated file
     */
    public function dumpExtras($filepath)
    {
        $this->dumpSassImports($filepath, $this->extraFiles);
    }

    /**
     * @param string $filepath
     */
    public function dumpTheme($filepath)
    {
        $this->dumpSassImports($filepath, [$this->theme.'/theme.scss']);
    }

    /**
     * @param string $filepath
     */
    public function dumpThemeVariables($filepath)
    {
        $this->dumpSassImports($filepath, [$this->theme.'/variables.scss']);
    }

    private function dumpSassImports($filename, array $paths)
    {
        $content = '';
        foreach ($paths as $path) {
            $content .= sprintf('@import "%s";'.PHP_EOL, $path);
        }

        $this->fs->dumpFile($filename, $content);
    }
}
