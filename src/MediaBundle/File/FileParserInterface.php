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
     * @return ParseResult A result containing the charset, mimetype, and extension
     */
    public function parse($pathname);
}
