<?php

namespace Perform\MediaBundle\Url;

use Perform\MediaBundle\Entity\File;

/**
 * Get a url to an uploaded file.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
interface FileUrlGeneratorInterface
{
    /**
     * @return string
     */
    public function getRootUrl();

    /**
     * @param string $filename
     *
     * @return string
     */
    public function getUrl($filename);
}
