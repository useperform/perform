<?php

namespace Perform\MediaBundle\File;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
interface FileParserInterface
{
    /**
     * Inspect a file and return the charset, mimetype and a suitable file extension.
     *
     * @param string $pathname
     *
     * @return array A list containing the charset, mimetype, and extension
     */
    public function parse($pathname);
}
