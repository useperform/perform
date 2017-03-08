<?php

namespace Perform\BaseBundle\Action;

use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * ActionRegistry
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class ActionRegistry
{
    protected $container;
    protected $actions = [];

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function addAction($name, $entityClass, $service)
    {
        $this->actions[$name] = [$entityClass, $service];
    }

    public function getAction($name)
    {
        if (!isset($this->actions[$name][1])) {
            throw new ActionNotFoundException(sprintf('Action "%s" has not been registered.', $name));
        }

        return $this->container->get($this->actions[$name][1]);
    }

    public function getTargetEntity($name)
    {
        if (!isset($this->actions[$name][0])) {
            throw new ActionNotFoundException(sprintf('Action "%s" has not been registered.', $name));
        }

        return $this->actions[$name][0];
    }
}
