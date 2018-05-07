<?php

namespace Perform\BaseBundle\Asset\Dumper;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class JavascriptTarget implements TargetInterface
{
    protected $filename;
    protected $imports = [];

    public function __construct($filename, array $imports)
    {
        $this->filename = $filename;
        $this->imports = $imports;
    }

    public function getFilename()
    {
        return $this->filename;
    }

    public function getContents()
    {
        $content = '';
        foreach ($this->imports as $name => $import) {
            $content .= sprintf("import %s from '%s';".PHP_EOL, $name, $import);
        }
        $content .= 'export default {'.PHP_EOL;
        foreach (array_keys($this->imports) as $name) {
            $content .= sprintf('%s,'.PHP_EOL, $name);
        }
        $content .= '}';

        return $content;
    }
}
