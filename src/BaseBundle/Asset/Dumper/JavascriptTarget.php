<?php

namespace Perform\BaseBundle\Asset\Dumper;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class JavascriptTarget
{
    protected $filename;
    protected $imports = [];

    public function __construct($filename, array $imports)
    {
        $this->filename = $filename;
        $this->imports = $imports;
    }

    /**
     * @return string The absolute path of the target file
     */
    public function getFilename()
    {
        return $this->filename;
    }

    /**
     * @return array An array of javascript imports, where the values
     * are the import paths, and keys are the names of the exposed
     * import variables.
     */
    public function getImports()
    {
        return $this->imports;
    }
}
