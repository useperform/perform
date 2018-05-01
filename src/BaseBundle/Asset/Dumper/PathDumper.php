<?php

namespace Perform\BaseBundle\Asset\Dumper;

use Symfony\Component\Filesystem\Filesystem;

/**
 * Generates asset-paths.js
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class PathDumper
{
    protected $fs;
    protected $namespaces = [];
    protected $entrypoints = [];

    public function __construct(Filesystem $fs, array $namespaces, array $entrypoints)
    {
        $this->fs = $fs;
        $this->namespaces = $namespaces;
        $this->entrypoints = $entrypoints;
    }

    /**
     * @param string $filepath The path of the generated file.
     */
    public function dump($filepath)
    {
        $data = [
            'entrypoints' => [],
            'namespaces' => [],
        ];
        foreach ($this->entrypoints as $name => $entry) {
            $data['entrypoints'][$name] = $entry;
        }
        foreach ($this->namespaces as $name => $path) {
            $data['namespaces'][$name] = rtrim($path, '/').'/';
        }
        $content = sprintf('module.exports = %s', json_encode($data, JSON_PRETTY_PRINT));

        $this->fs->dumpFile($filepath, $content);
    }
}
