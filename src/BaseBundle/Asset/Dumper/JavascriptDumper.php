<?php

namespace Perform\BaseBundle\Asset\Dumper;

use Symfony\Component\Filesystem\Filesystem;

/**
 * Dump modules.js, added to the window.Perform object.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class JavascriptDumper
{
    protected $fs;
    protected $javascriptModules = [];

    public function __construct(Filesystem $fs, array $javascriptModules)
    {
        $this->fs = $fs;
        $this->javascriptModules = $javascriptModules;
    }

    /**
     * @param string $filepath The path of the generated file.
     */
    public function dump($filepath)
    {
        $content = '';
        foreach ($this->javascriptModules as $name => $import) {
            $content .= sprintf("import %s from '%s';".PHP_EOL, $name, $import);
        }
        $content .= 'export default {'.PHP_EOL;
        foreach (array_keys($this->javascriptModules) as $name) {
            $content .= sprintf('%s,'.PHP_EOL, $name);
        }
        $content .= '}';

        $this->fs->dumpFile($filepath, $content);
    }
}
