<?php

namespace Perform\DevBundle\Packaging;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class NpmMergeResult
{
    protected $resolvedRequirements;
    protected $newRequirements;
    protected $unresolvedRequirements;

    public function __construct(array $resolvedRequirements, array $newRequirements, array $unresolvedRequirements)
    {
        $this->resolvedRequirements = $resolvedRequirements;
        $this->newRequirements = $newRequirements;
        $this->unresolvedRequirements = $unresolvedRequirements;
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
}
