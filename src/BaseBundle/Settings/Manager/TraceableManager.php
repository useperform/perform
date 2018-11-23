<?php

namespace Perform\BaseBundle\Settings\Manager;

use Perform\BaseBundle\Exception\SettingNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;

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

    public function getUserValue(UserInterface $user, $key, $default = null)
    {
        try {
            $value = $this->manager->getRequiredUserValue($user, $key);
            $this->getCalls[] = [$key, $value, true, $user];

            return $value;
        } catch (SettingNotFoundException $e) {
            $this->getCalls[] = [$key, $default, false, $user];

            return $default;
        }
    }

    public function getRequiredUserValue(UserInterface $user, $key)
    {
        $value = $this->manager->getRequiredUserValue($user, $key);
        $this->getCalls[] = [$key, $value, true, $user];

        return $value;
    }

    public function setUserValue(UserInterface $user, $key, $value)
    {
        $this->setCalls[] = [$key, $value, $user];

        return $this->manager->setUserValue($user, $key, $value);
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
