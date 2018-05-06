<?php

namespace Perform\BaseBundle\Asset\Dumper;

use Symfony\Component\Filesystem\Filesystem;

/**
 * Generate asset-paths.js.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class PathDumper
{
    protected $fs;

    public function __construct(Filesystem $fs)
    {
        $this->fs = $fs;
    }

    /**
     * @param PathTarget $target
     */
    public function dump(PathTarget $target)
    {
        $data = [
            'entrypoints' => [],
            'namespaces' => [],
        ];
        foreach ($target->getEntrypoints() as $name => $entry) {
            $data['entrypoints'][$name] = $entry;
        }
        foreach ($target->getNamespaces() as $name => $path) {
            $data['namespaces'][$name] = rtrim($path, '/').'/';
        }
        $content = sprintf('module.exports = %s', json_encode($data, JSON_PRETTY_PRINT));

        $this->fs->dumpFile($target->getFilename(), $content);
    }
}
