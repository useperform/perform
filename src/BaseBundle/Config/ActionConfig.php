<?php

namespace Perform\BaseBundle\Config;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Perform\BaseBundle\Util\StringUtil;
use Perform\BaseBundle\Admin\AdminRequest;
use Perform\BaseBundle\Action\ActionRegistry;
use Perform\BaseBundle\Action\ConfiguredAction;
use Perform\BaseBundle\Action\ActionNotFoundException;
use Perform\BaseBundle\Action\LinkAction;
use Perform\BaseBundle\Action\ActionInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class ActionConfig
{
    protected $registry;
    protected $authChecker;
    protected $resolver;
    protected $actions = [];
    protected $linkIndex = 0;

    public function __construct(ActionRegistry $registry, AuthorizationCheckerInterface $authChecker)
    {
        $this->registry = $registry;
        $this->authChecker = $authChecker;
        $this->resolver = new OptionsResolver();
        $this->resolver
            ->setDefined('label')
            ->setAllowedTypes('label', ['string', 'Closure'])
            ->setDefined('batchLabel')
            ->setAllowedTypes('batchLabel', ['string', 'Closure'])
            ->setDefaults([
                'isGranted' => true,
                'isButtonAvailable' => true,
                'isBatchOptionAvailable' => true,
                'confirmationRequired' => false,
                'confirmationMessage' => function ($entity, $label) {
                    return sprintf('Are you sure you want to %s this item?', strtolower($label));
                },
                'buttonStyle' => 'btn-default',
            ])
            ->setAllowedTypes('isGranted', ['bool', 'Closure'])
            ->setAllowedTypes('isButtonAvailable', ['bool', 'Closure'])
            ->setAllowedTypes('isBatchOptionAvailable', ['bool', 'Closure'])
            ->setAllowedTypes('confirmationMessage', ['string', 'Closure'])
            ->setAllowedTypes('confirmationRequired', ['bool', 'Closure'])
            ->setAllowedTypes('buttonStyle', 'string')
            ->setDefined('link')
            ->setAllowedTypes('link', 'Closure')
            ;
    }

    /**
     * Add a named action from the action registry for this entity type.
     *
     * Use the `perform:debug:actions` command to view the list of action names.
     *
     * @param string $name
     * @param array  $options
     */
    public function add($name, array $options = [])
    {
        return $this->addInstance($name, $this->registry->getAction($name), $options);
    }

    /**
     * Add an action instance for this entity type.
     *
     * @param string $name
     * @param array  $options
     */
    public function addInstance($name, ActionInterface $action, array $options = [])
    {
        $options = array_replace_recursive($action->getDefaultConfig(), $options);

        if (!isset($options['label'])) {
            $options['label'] = StringUtil::sensible($name);
        }
        if (!isset($options['batchLabel'])) {
            $options['batchLabel'] = is_string($options['label']) ?
                                   $options['label'] : StringUtil::sensible($name);
        }

        $options = $this->resolver->resolve($options);

        $maybeClosures = [
            'isGranted',
            'isButtonAvailable',
            'isBatchOptionAvailable',
            'label',
            'batchLabel',
            'confirmationRequired',
            'confirmationMessage',
        ];

        foreach ($maybeClosures as $key) {
            if (!$options[$key] instanceof \Closure) {
                $value = $options[$key];
                $options[$key] = function () use ($value) {
                    return $value;
                };
            }
        }

        $this->actions[$name] = new ConfiguredAction($name, $action, $options);

        return $this;
    }

    /**
     * Add a link for this entity type.
     *
     * @param string|Closure $link    The url of the link
     * @param string|Closure $label
     * @param array          $options
     */
    public function addLink($link, $label, array $options = [])
    {
        $options['link'] = $link instanceof \Closure ? $link : function () use ($link) {
            return $link;
        };
        $options['label'] = $label;

        $name = 'link_'.$this->linkIndex;
        ++$this->linkIndex;

        return $this->addInstance($name, new LinkAction(), $options);
    }

    public function get($name)
    {
        if (!isset($this->actions[$name])) {
            throw new ActionNotFoundException(sprintf('Action "%s" has not been registered with this ActionConfig.', $name));
        }

        return $this->actions[$name];
    }

    /**
     * @return ConfiguredAction[]
     */
    public function all()
    {
        return $this->actions;
    }

    /**
     * Get an array of actions that are allowed to be ran for the given entity.
     *
     * @param object $entity
     *
     * @return ConfiguredAction[]
     */
    public function getForEntity($entity)
    {
        $allowed = [];
        foreach ($this->actions as $action) {
            if ($action->isGranted($entity, $this->authChecker)) {
                $allowed[] = $action;
            }
        }

        return $allowed;
    }

    /**
     * Get an array of actions to be used for showing buttons for an
     * entity.
     * This method is purely presentational; the presence of a button
     * doesn't guarantee the action will be granted for the entity.
     *
     * @param object       $entity
     * @param AdminRequest $request
     *
     * @return ConfiguredAction[]
     */
    public function getButtonsForEntity($entity, AdminRequest $request)
    {
        $allowed = [];
        foreach ($this->actions as $action) {
            if ($action->isButtonAvailable($entity, $request) && $action->isGranted($entity, $this->authChecker)) {
                $allowed[] = $action;
            }
        }

        return $allowed;
    }

    /**
     * Get an array of actions to be used when displaying a list of
     * batch action options.
     * This method is purely presentational; the presence of an option
     * doesn't guarantee the action will be granted.
     *
     * @param AdminRequest $request
     *
     * @return ConfiguredAction[]
     */
    public function getBatchOptionsForRequest(AdminRequest $request)
    {
        $allowed = [];
        foreach ($this->actions as $action) {
            if ($action->isBatchOptionAvailable($request)) {
                $allowed[] = $action;
            }
        }

        return $allowed;
    }
}
