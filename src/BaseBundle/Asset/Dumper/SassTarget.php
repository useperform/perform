<?php

namespace Perform\BaseBundle\Asset\Dumper;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class SassTarget implements TargetInterface
{
    protected $filename;
    protected $paths = [];

    public function __construct($filename, array $paths)
    {
        $this->filename = $filename;
        $this->paths = $paths;
    }

    public function getFilename()
    {
        return $this->filename;
    }

    public function getContents()
    {
        $content = '';
        foreach ($this->paths as $path) {
            $content .= sprintf('@import "%s";'.PHP_EOL, $path);
        }

        return $content;
    }
}
