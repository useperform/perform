<?php

namespace Perform\Base\Type;

use Perform\Base\Exception\TypeNotFoundException;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * TypeRegistry.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class TypeRegistry
{
    protected $container;
    protected $classes = [];
    protected $instances = [];
    protected $services = [];

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function addType($name, $classname)
    {
        $this->classes[$name] = $classname;
    }

    public function addTypeService($name, $service)
    {
        $this->services[$name] = $service;
    }

    public function getType($name)
    {
        //services have priority
        if (isset($this->services[$name])) {
            return $this->container->get($this->services[$name]);
        }

        if (isset($this->classes[$name])) {
            $classname = $this->classes[$name];
            if (!isset($this->instances[$name])) {
                $this->instances[$name] = new $classname();
            }

            return $this->instances[$name];
        }

        throw new TypeNotFoundException(sprintf('Entity field type not found: "%s"', $name));
    }

    public function getAvailableTypes()
    {
        return array_keys(array_merge($this->classes, $this->services));
    }
}
