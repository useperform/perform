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
    protected $actionsByClass = [];

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function addAction($name, $entityClass, $service)
    {
        $this->actions[$name] = [$entityClass, $service];
        $this->actionsByClass[$entityClass][] = [$name, $service];
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

    public function getActionsForEntityClass($class)
    {
        $actions = isset($this->actionsByClass[$class]) ? $this->actionsByClass[$class] : [];

        $results = [];
        foreach ($actions as $action) {
            $results[$action[0]] = $this->container->get($action[1]);
        }

        return $results;
    }

    public function getActionsForEntity($entity)
    {
        $actions = $this->getActionsForEntityClass(get_class($entity));

        return array_filter($actions, function($action) use ($entity) {
            return $action->isGranted($entity);
        });
    }
}
