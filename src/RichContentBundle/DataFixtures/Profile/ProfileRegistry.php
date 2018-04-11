<?php

namespace Perform\RichContentBundle\DataFixtures\Profile;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class ProfileRegistry
{
    protected $profiles;

    public function __construct(array $profiles)
    {
        $this->profiles = $profiles;
    }

    public function all()
    {
        return $this->profiles;
    }

    public function get($name)
    {
        if (!isset($this->profiles[$name])) {
            throw new ProfileNotFoundException(sprintf('Rich content fixture profile "%s" was not found. To create a new profile, create a service that implements %s, then have the getName() method return "%s".', $name, ProfileInterface::class, $name));
        }

        return $this->profiles[$name];
    }

    public function getRandom()
    {
        if (count($this->profiles) < 1) {
            throw new ProfileNotFoundException('Unable to get a random fixture profile because none are registered.');
        }

        return $this->profiles[array_rand($this->profiles)];
    }
}
