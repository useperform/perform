<?php

namespace Perform\BaseBundle\Asset\Dumper;

use Symfony\Component\Filesystem\Filesystem;

/**
 * Generate dynamic javascript imports.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class JavascriptDumper
{
    protected $fs;

    public function __construct(Filesystem $fs)
    {
        $this->fs = $fs;
    }

    /**
     * @param JavascriptTarget $target
     */
    public function dump(JavascriptTarget $target)
    {
        $content = '';
        foreach ($target->getImports() as $name => $import) {
            $content .= sprintf("import %s from '%s';".PHP_EOL, $name, $import);
        }
        $content .= 'export default {'.PHP_EOL;
        foreach (array_keys($target->getImports()) as $name) {
            $content .= sprintf('%s,'.PHP_EOL, $name);
        }
        $content .= '}';

        $this->fs->dumpFile($target->getFilename(), $content);
    }
}
