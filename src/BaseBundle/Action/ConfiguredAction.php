<?php

namespace Perform\BaseBundle\Action;

use Perform\BaseBundle\Crud\CrudRequest;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Perform\BaseBundle\Routing\CrudUrlGeneratorInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Represents an action configured with options from crud classes.
 *
 * This class shouldn't be constructed manually; get one from
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

    public function getLabel(CrudRequest $request, $entity)
    {
        return call_user_func($this->options['label'], $request, $entity);
    }

    public function getBatchLabel(CrudRequest $request)
    {
        return call_user_func($this->options['batchLabel'], $request);
    }

    /**
     * Return true if this action is allowed to be run on the given entity.
     *
     * @param object $entity
     *
     * @return bool
     */
    public function isGranted($entity, AuthorizationCheckerInterface $authChecker)
    {
        return (bool) $this->options['isGranted']($entity, $authChecker);
    }

    /**
     * Return true if the button for this action should be shown for this entity.
     * Note that this does not guarantee the action will be allowed on the entity.
     * The result of isGranted() will be used for that.
     *
     * @param object       $entity
     * @param CrudRequest $request
     *
     * @return bool
     */
    public function isButtonAvailable($entity, CrudRequest $request)
    {
        return (bool) $this->options['isButtonAvailable']($entity, $request);
    }

    public function isBatchOptionAvailable(CrudRequest $request)
    {
        return (bool) $this->options['isBatchOptionAvailable']($request);
    }

    public function isConfirmationRequired()
    {
        return (bool) $this->options['confirmationRequired']();
    }

    /**
     * @return bool
     */
    public function isLink()
    {
        return isset($this->options['link']);
    }

    /**
     * Get the URL of the link.
     *
     * The URL may change depending on the entity, and may be
     * generated from the supplied $crudUrlGenerator and
     * $urlGenerator.
     *
     * @param object                    $entity
     * @param CrudUrlGeneratorInterface $crudUrlGenerator
     * @param UrlGeneratorInterface     $urlGenerator
     *
     * @return string
     */
    public function getLink($entity, CrudUrlGeneratorInterface $crudUrlGenerator, UrlGeneratorInterface $urlGenerator)
    {
        return $this->isLink() ? $this->options['link']($entity, $crudUrlGenerator, $urlGenerator) : '';
    }

    public function getConfirmationMessage(CrudRequest $request, $entity)
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
