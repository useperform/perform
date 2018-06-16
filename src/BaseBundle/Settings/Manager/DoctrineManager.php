<?php

namespace Perform\BaseBundle\Settings\Manager;

use Perform\BaseBundle\Exception\SettingNotFoundException;
use Perform\BaseBundle\Repository\SettingRepository;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class DoctrineManager implements SettingsManagerInterface
{
    protected $repo;

    public function __construct(SettingRepository $repo)
    {
        $this->repo = $repo;
    }

    public function getValue($key, $default = null)
    {
        try {
            return $this->getRequiredValue($key);
        } catch (SettingNotFoundException $e) {
            return $default;
        }
    }

    public function getRequiredValue($key)
    {
        return $this->repo->getRequiredValue($key);
    }

    public function setValue($key, $value)
    {
        return $this->repo->setValue($key, $value);
    }
}
