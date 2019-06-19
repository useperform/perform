<?php

namespace Perform\DevBundle\File;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Yaml\Yaml;

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

    /**
     * Replace the top level section in the file with new yaml.
     *
     * If the section is not found, the yaml will be appended to the file.
     *
     * Note that $yaml should include the section name; it will not be added automatically.
     *
     * @param string $name
     * @param string $yaml
     */
    public function replaceSection($name, $yaml)
    {
        $contents = file_get_contents($this->configFile);

        // match all lines starting from <name>: to the first empty
        // line (or line made up of spaces).

        // the alternative pattern (after the |) is the same without the
        // empty line at the end, for when a section is at the end of a file.

        // m for multiple
        // s for matching newlines with .
        $sectionPattern = sprintf('/(^%s:.+^\w*$)|(^%s:.+)/ms', $name, $name);
        $contents = preg_replace($sectionPattern, $yaml, $contents, 1, $count);

        if ($count === 0) {
            $contents .= PHP_EOL.$yaml;
        }

        return $this->fs->dumpFile($this->configFile, $contents);
    }
}