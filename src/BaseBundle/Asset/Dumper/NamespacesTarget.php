<?php

namespace Perform\BaseBundle\Asset\Dumper;

/**
 * Generates assets/namespaces.js.
 **/
class NamespacesTarget implements TargetInterface
{
    private $filename;
    private $namespaces = [];

    public function __construct($filename, array $namespaces)
    {
        $this->filename = $filename;
        $this->namespaces = $namespaces;
    }

    public function getFilename()
    {
        return $this->filename;
    }

    public function getContents()
    {
        foreach ($this->namespaces as $name => $path) {
            $data[$name] = rtrim($path, '/').'/';
        }

        return sprintf('module.exports = %s'.PHP_EOL, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
    }
}
