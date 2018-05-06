<?php

namespace Perform\BaseBundle\Asset\Dumper;

use Symfony\Component\Filesystem\Filesystem;

/**
 * Generate dynamic sass imports.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class SassDumper
{
    protected $fs;

    public function __construct(Filesystem $fs)
    {
        $this->fs = $fs;
    }

    /**
     * @param SassTarget $target
     */
    public function dump(SassTarget $target)
    {
        $content = '';
        foreach ($target->getPaths() as $path) {
            $content .= sprintf('@import "%s";'.PHP_EOL, $path);
        }

        $this->fs->dumpFile($target->getFilename(), $content);
    }
}
