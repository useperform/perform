<?php

namespace Perform\DevBundle\Packaging;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class NpmMergeResultCollection
{
    protected $existingRequirements = [];
    protected $resolvedRequirements = [];
    protected $newRequirements = [];
    protected $unresolvedRequirements = [];

    public function __construct(array $existingRequirements)
    {
        $this->existingRequirements = $existingRequirements;
        $this->resolvedRequirements = $existingRequirements;
    }

    public function addResult(NpmMergeResult $result)
    {
        $this->resolvedRequirements = $result->getResolvedRequirements();
        $this->newRequirements = array_merge($this->newRequirements, $result->getNewRequirements());
        $this->unresolvedRequirements = array_merge($this->unresolvedRequirements, $result->getUnresolvedRequirements());
    }

    public function getResolvedRequirements()
    {
        return $this->resolvedRequirements;
    }

    public function getNewRequirements()
    {
        return $this->newRequirements;
    }

    public function getUnresolvedRequirements()
    {
        return $this->unresolvedRequirements;
    }

    public function hasChanges()
    {
        return !empty($this->newRequirements);
    }

    public function hasNew()
    {
        return !empty($this->newRequirements);
    }

    public function hasUnresolved()
    {
        return !empty($this->unresolvedRequirements);
    }
}
