<?php

namespace Perform\BaseBundle\Asset\Dumper;

use Symfony\Component\Filesystem\Filesystem;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class Dumper
{
    protected $fs;

    public function __construct(Filesystem $fs)
    {
        $this->fs = $fs;
    }

    /**
     * @param TargetInterface $target
     */
    public function dump(TargetInterface $target)
    {
        $this->fs->dumpFile($target->getFilename(), $target->getContents());
    }
}
