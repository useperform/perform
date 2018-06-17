<?php

namespace Perform\BaseBundle\Settings\Manager;

use Perform\BaseBundle\Exception\SettingNotFoundException;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class TraceableManager implements SettingsManagerInterface
{
    protected $manager;

    protected $getCalls = [];
    protected $setCalls = [];

    public function __construct(SettingsManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    public function getInnerManager()
    {
        return $this->manager;
    }

    public function getValue($key, $default = null)
    {
        try {
            $value = $this->manager->getRequiredValue($key);
            $this->getCalls[] = [$key, $value, true];

            return $value;
        } catch (SettingNotFoundException $e) {
            $this->getCalls[] = [$key, $default, false];

            return $default;
        }
    }

    public function getRequiredValue($key)
    {
        $value = $this->manager->getRequiredValue($key);
        $this->getCalls[] = [$key, $value, true];

        return $value;
    }

    public function setValue($key, $value)
    {
        $this->setCalls[] = [$key, $value];

        return $this->manager->setValue($key, $value);
    }

    public function getGetCalls()
    {
        return $this->getCalls;
    }

    public function getSetCalls()
    {
        return $this->setCalls;
    }
}
