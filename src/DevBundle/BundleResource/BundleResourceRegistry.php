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
    protected $parents = [];

    public function addResource(BundleResourceInterface $resource)
    {
        $this->resources[$resource->getBundleName()] = $resource;
    }

    public function getResources()
    {
        return $this->resources;
    }

    public function addParentResource(BundleResourceInterface $resource)
    {
        $this->addResource($resource);
        $this->parents[$resource->getBundleName()] = $resource;
    }

    public function getParentResources()
    {
        return $this->parents;
    }
}
