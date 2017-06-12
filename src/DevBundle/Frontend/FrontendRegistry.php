<?php

namespace Perform\DevBundle\Frontend;

/**
 * FrontendRegistry.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class FrontendRegistry
{
    protected $frontends = [];

    public function add(FrontendInterface $frontend)
    {
        $this->frontends[$frontend->getName()] = $frontend;
    }

    public function get($name)
    {
        if (!isset($this->frontends[$name])) {
            throw new \Exception(sprintf('Unknown scaffolding frontend "%s".', $name));
        }

        return $this->frontends[$name];
    }

    public function all()
    {
        return $this->frontends;
    }
}
