<?php

namespace Perform\BaseBundle\Action;

/**
 * ConfiguredAction
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class ConfiguredAction
{
    public function __construct($name, ActionInterface $action, \Closure $label, \Closure $batchLabel)
    {
        $this->name = $name;
        $this->action = $action;
        $this->label = $label;
        $this->batchLabel = $batchLabel;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getLabel($entity)
    {
        return call_user_func($this->label, $entity);
    }

    public function getBatchLabel()
    {
        return call_user_func($this->batchLabel);
    }

    public function isGranted($entity)
    {
        //also check with any custom code passed in
        return $this->action->isGranted($entity);
    }

    public function run($entities)
    {
        //any other configured options passed into this class
        $options = [];
        return $this->action->run($entities, $options);
    }
}
