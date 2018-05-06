<?php

namespace Perform\BaseBundle\Asset\Dumper;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class SassTarget
{
    protected $filename;
    protected $paths = [];

    public function __construct($filename, array $paths)
    {
        $this->filename = $filename;
        $this->paths = $paths;
    }

    /**
     * @return string The absolute path of the target file
     */
    public function getFilename()
    {
        return $this->filename;
    }

    /**
     * @return array An array of scss files to be imported into the file
     */
    public function getPaths()
    {
        return $this->paths;
    }
}
