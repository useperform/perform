<?php

namespace Perform\BaseBundle\Action;

use Perform\BaseBundle\DependencyInjection\LoopableServiceLocator;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class ActionRegistry
{
    protected $actions;

    public function __construct(LoopableServiceLocator $actions)
    {
        $this->actions = $actions;
    }

    /**
     * Get an action service by name.
     *
     * @return ActionInterface
     */
    public function get($name)
    {
        if (!$this->actions->has($name)) {
            throw new ActionNotFoundException(sprintf('Action "%s" is not registered. Use the perform:debug:actions command to see the available actions.', $name));
        }

        return $this->actions->get($name);
    }

    /**
     * @return LoopableServiceLocator
     */
    public function all()
    {
        return $this->actions;
    }
}
