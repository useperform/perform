<?php

namespace Perform\MediaBundle\Url;
use Perform\MediaBundle\Entity\File;

/**
 * SimpleFileUrlGenerator
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class SimpleFileUrlGenerator implements FileUrlGeneratorInterface
{
    protected $rootUrl;

    public function __construct($rootUrl)
    {
        $this->rootUrl = rtrim($rootUrl, '/').'/';
    }

    public function getRootUrl()
    {
        return $this->rootUrl;
    }

    public function getUrl($filename)
    {
        return $this->rootUrl . $filename;
    }
}
