<?php

namespace Perform\DevBundle\BundleResource;

/**
 * BundleResourceRegistry.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class BundleResourceRegistry
{
    protected $resources = [];

    public function addResource(BundleResourceInterface $resource)
    {
        $this->resources[] = $resource;
    }

    public function all()
    {
        return $this->resources;
    }
}
