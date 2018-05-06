<?php

namespace Perform\BaseBundle\Asset\Dumper;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class PathTarget
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

    /**
     * @return string The absolute path of the target file
     */
    public function getFilename()
    {
        return $this->filename;
    }

    /**
     * @return array An array of namespaces, where the keys are
     * namespace names and values are absolute paths to file
     * directories.
     */
    public function getNamespaces()
    {
        return $this->namespaces;
    }

    /**
     * @return array An array of entrypoints, where the keys are
     * asset names and values are arrays of absolute file paths.
     */
    public function getEntrypoints()
    {
        return $this->entrypoints;
    }
}
