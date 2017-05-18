<?php

namespace Perform\DevBundle\File;

use Symfony\Component\Filesystem\Filesystem;

/**
 * YamlModifier.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class YamlModifier
{
    protected $configFile;

    public function __construct($configFile)
    {
        $this->configFile = $configFile;
        $this->fs = new Filesystem();
    }

    /**
     * Add yaml to the file.
     * Yaml will not be added if it is already detected in the file.
     *
     * If a $checkPattern regex is supplied, search the file for this
     * regex instead of the yaml.
     *
     * @param string $yaml
     * @param string $checkPattern A regular expression
     */
    public function addConfig($yaml, $checkPattern = null)
    {
        $contents = file_get_contents($this->configFile);

        if ($checkPattern && preg_match($checkPattern, $contents)) {
            //matches check pattern, so quit
            return;
        }

        if (!$checkPattern && strpos($contents, trim($yaml))) {
            //already added
            return;
        }

        return $this->fs->dumpFile($this->configFile, $contents.$yaml);
    }
}
