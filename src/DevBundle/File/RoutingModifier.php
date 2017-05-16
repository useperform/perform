<?php

namespace Perform\DevBundle\File;

use Symfony\Component\Filesystem\Filesystem;

/**
 * RoutingModifier.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class RoutingModifier
{
    protected $configFile;

    public function __construct($configFile)
    {
        $this->configFile = $configFile;
        $this->fs = new Filesystem();
    }

    public function addConfig($yaml, $checkPattern = null)
    {
        $contents = file_get_contents($this->configFile);

        if ($checkPattern && preg_match($checkPattern, $contents)) {
            //matches check pattern, so quit
            return;
        }

        if (strpos($contents, trim($yaml))) {
            //already added
            return;
        }

        return $this->fs->dumpFile($this->configFile, $contents.$yaml);
    }
}
