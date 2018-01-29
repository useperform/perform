<?php

namespace Perform\MediaBundle\Url;

use Perform\MediaBundle\Location\Location;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class SimpleUrlGenerator implements UrlGeneratorInterface
{
    protected $rootUrl;

    public function __construct($rootUrl)
    {
        $this->rootUrl = rtrim($rootUrl, '/').'/';
    }

    public function generate(Location $location)
    {
        return $this->rootUrl.$location->getPath();
    }
}
