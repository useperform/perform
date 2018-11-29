<?php

namespace Perform\BaseBundle\Settings\Manager;

use Perform\BaseBundle\Exception\SettingNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Perform\BaseBundle\Exception\ReadOnlySettingsException;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class TraceableManager implements SettingsManagerInterface, WriteableSettingsManagerInterface
{
    protected $manager;
    protected $writeable;

    protected $getCalls = [];
    protected $setCalls = [];

    public function __construct(SettingsManagerInterface $manager)
    {
        $this->manager = $manager;
        $this->writeable = $manager instanceof WriteableSettingsManagerInterface;
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
        $this->assertWriteable();
        $this->setCalls[] = [$key, $value];

        return $this->manager->setValue($key, $value);
    }

    private function assertWriteable()
    {
        if (!$this->writeable) {
            throw new ReadOnlySettingsException(sprintf('%s is read-only. You should create a manager that implements %s to write settings.', get_class($this->manager), WriteableSettingsManagerInterface::class));
        }
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
        $this->assertWriteable();
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
