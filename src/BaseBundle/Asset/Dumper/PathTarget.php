<?php

namespace Perform\BaseBundle\Asset\Dumper;

/**
 * Target implementation for asset-paths.js.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class PathTarget implements TargetInterface
{
    protected $filename;
    protected $namespaces = [];
    protected $entrypoints = [];

    public function __construct($filename, array $namespaces, array $entrypoints)
    {
        $this->filename = $filename;
        $this->namespaces = $namespaces;
        $this->entrypoints = $entrypoints;
    }

    public function getFilename()
    {
        return $this->filename;
    }

    public function getContents()
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

        return sprintf('module.exports = %s', json_encode($data, JSON_PRETTY_PRINT));
    }
}
