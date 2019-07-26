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
        $lines = [];
        foreach ($this->namespaces as $name => $path) {
            $lines[] = sprintf('    "%s": path.resolve(__dirname, "../", "%s")', $name, rtrim($path, '/').'/');
        }

        $joinedLines = implode(','.PHP_EOL, $lines);

        return <<<EOF
var path = require('path');

module.exports = {
${joinedLines}
}

EOF;
    }
}
