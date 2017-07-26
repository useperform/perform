<?php

namespace Perform\BaseBundle\Action;

use Perform\BaseBundle\Admin\AdminRequest;

/**
 * Represents an action configured with options from admin classes.
 *
 * This class shouldn't need to be constructed manually; get one from
 * ActionConfig instead.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class ConfiguredAction
{
    protected $name;
    protected $action;
    protected $options;

    public function __construct($name, ActionInterface $action, array $options)
    {
        $this->name = $name;
        $this->action = $action;
        $this->options = $options;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getLabel(AdminRequest $request, $entity)
    {
        return call_user_func($this->options['label'], $request, $entity);
    }

    public function getBatchLabel(AdminRequest $request)
    {
        return call_user_func($this->options['batchLabel'], $request);
    }

    public function isGranted($entity)
    {
        //also check with any custom code passed in
        return $this->action->isGranted($entity);
    }

    public function isAvailable(AdminRequest $request)
    {
        return $this->action->isAvailable($request);
    }

    public function isConfirmationRequired()
    {
        return (bool) $this->options['confirmationRequired']();
    }

    public function getConfirmationMessage(AdminRequest $request, $entity)
    {
        return $this->options['confirmationMessage']($entity, $this->getLabel($request, $entity));
    }

    public function getButtonStyle()
    {
        return $this->options['buttonStyle'];
    }

    public function run($entities, array $options = [])
    {
        return $this->action->run($entities, $options);
    }
}
