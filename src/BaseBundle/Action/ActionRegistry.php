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
    protected $actions;

    public function __construct(ContainerInterface $container, $actions)
    {
        $this->container = $container;
        $this->actions = $actions;
    }

    public function getAction($name)
    {
        if (!isset($this->actions[$name])) {
            throw new ActionNotFoundException(sprintf('Action "%s" has not been registered.', $name));
        }

        return $this->container->get($this->actions[$name]);
    }
}
