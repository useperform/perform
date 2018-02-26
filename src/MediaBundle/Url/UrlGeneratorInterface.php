<?php

namespace Perform\MediaBundle\Url;

use Perform\MediaBundle\Entity\Location;

/**
 * Resolve Locations to URLs.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
interface UrlGeneratorInterface
{
    /**
     * @param Location $location
     *
     * @return string
     */
    public function generate(Location $location);
}
