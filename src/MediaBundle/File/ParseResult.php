<?php

namespace Perform\MediaBundle\File;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class ParseResult
{
    protected $mimeType;
    protected $charset;
    protected $extension;

    public function __construct($mimeType, $charset, $extension)
    {
        $this->mimeType = $mimeType;
        $this->charset = $charset;
        $this->extension = $extension;
    }

    public function getMimeType()
    {
        return $this->mimeType;
    }

    public function getCharset()
    {
        return $this->charset;
    }

    public function getExtension()
    {
        return $this->extension;
    }
}
