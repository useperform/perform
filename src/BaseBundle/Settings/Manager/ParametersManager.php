<?php

namespace Perform\BaseBundle\Settings\Manager;

use Perform\BaseBundle\Exception\SettingNotFoundException;
use Psr\Container\ContainerInterface;
use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;

/**
 * A read-only manager that fetches settings from parameters in the container.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class ParametersManager implements SettingsManagerInterface
{
    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
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
        try {
            return $this->container->getParameter($key);
        } catch (InvalidArgumentException $e) {
            throw new SettingNotFoundException(sprintf('Unable to read setting "%s", it is not a container parameter.', $key));
        }
    }

    public function setValue($key, $value)
    {
        throw new \Exception(__CLASS__.' is read-only; settings cannot be written with it.');
    }
}
