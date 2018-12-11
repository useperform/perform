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
        // do a hash check first, only dump if the contents have changed
        // this stops processes watching the file performing work for no reason
        // (e.g. webpack rebuilding assets on change)
        $newHash = md5($target->getContents());
        $existingHash = file_exists($target->getFilename()) ? md5(file_get_contents($target->getFilename())) : '';
        if ($existingHash !== $newHash) {
            $this->fs->dumpFile($target->getFilename(), $target->getContents());
        }
    }
}
