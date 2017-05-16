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

    public function addResource(ResourceInterface $resource)
    {
        $this->resources[$resource->getBundleName()] = $resource;
    }

    public function getResources()
    {
        return $this->resources;
    }

    public function addParentResource(ParentResourceInterface $resource)
    {
        $this->addResource($resource);
        $this->parents[$resource->getBundleName()] = $resource;
    }

    public function getParentResources()
    {
        return $this->parents;
    }

    /**
     * Get all the required resources for the given bundle names, dependencies first.
     *
     * @return ResourceInterface[]
     */
    public function resolveResources(array $bundleNames)
    {
        $resolved = [];
        //object hashes to prevent recursive explosion when comparing objects with in_array
        $used = [];

        foreach ($bundleNames as $bundle) {
            if (!isset($this->resources[$bundle])) {
                throw new \Exception(sprintf('Unknown dev bundle resource "%s"', $bundle));
            }

            $resource = $this->resources[$bundle];

            //depth first search of dependencies if it is a parent resource
            if ($resource instanceof ParentResourceInterface) {
                foreach ($this->resolveResources($resource->getRequiredBundles()) as $dep) {
                    if (!in_array(spl_object_hash($dep), $used)) {
                        $resolved[] = $dep;
                        $used[] = spl_object_hash($dep);
                    }
                }
            }

            if (!in_array(spl_object_hash($resource), $used)) {
                $resolved[] = $resource;
                $used[] = spl_object_hash($resource);
            }
        }

        return $resolved;
    }
}
