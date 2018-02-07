<?php

namespace Perform\MediaBundle\File;

use Mimey\MimeTypes;

/**
 * Parse files using the fileinfo extension.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class FinfoParser implements FileParserInterface
{
    protected $mimes;

    public function __construct()
    {
        $this->mimes = new MimeTypes();
    }

    public function parse($pathname)
    {
        $extension = pathinfo($pathname, PATHINFO_EXTENSION);
        $finfo = new \Finfo(FILEINFO_MIME);
        $guess = explode('; charset=', @$finfo->file($pathname));

        if (count($guess) === 2) {
            return [
                $guess[0],
                $guess[1],
                $this->validateExtension($guess[0], $extension),
            ];
        }

        // detection failed, so treat the file as binary
        $mime = 'application/octet-stream';

        return [
            $mime,
            'binary',
            $extension ?: $this->validateExtension($mime),
        ];
    }

    /**
     * Ensure the supplied extension is suitable for a file with the given mime type.
     *
     * If no extension is supplied, suggest a suitable extension for that mime type.
     *
     * @param string $mimeType  The mime type of the supplied file
     * @param string $extension The extension of the supplied file
     */
    protected function validateExtension($mimeType, $extension)
    {
        $validExtensions = $this->mimes->getAllExtensions($mimeType);

        // can't suggest any better
        if (!isset($validExtensions[0])) {
            return $extension;
        }

        // no extension supplied, provide a guess
        if (!$extension) {
            return $validExtensions[0];
        }

        // extension is approved
        if (in_array($extension, $validExtensions)) {
            return $extension;
        }

        // finfo may suggest text/plain for many types, e.g. yaml, html.twig, toml.
        // If the suggested mime type for the extension is not
        // text/*, return a more appropriate extension instead.

        // This allows for most extensions to remain unchanged, but
        // also prevents weird stuff like a text file ending in .jpg

        // e.g.
        // config.yml -> config.yml,
        // plain-text.jpg -> plain-text.txt
        $suggestedMime = $this->mimes->getMimeType($extension);
        if ($suggestedMime && substr($suggestedMime, 0, 5) !== 'text/') {
            return $validExtensions[0];
        }

        return $extension;
    }
}
