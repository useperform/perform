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

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class ActionConfig
{
    protected $registry;
    protected $resolver;
    protected $actions = [];
    protected $linkIndex = 0;

    public function __construct(ActionRegistry $registry)
    {
        $this->registry = $registry;
        $this->resolver = new OptionsResolver();
        $this->resolver
            ->setDefined('label')
            ->setAllowedTypes('label', ['string', 'Closure'])
            ->setDefined('batchLabel')
            ->setAllowedTypes('batchLabel', ['string', 'Closure'])
            ->setDefaults([
                'confirmationRequired' => false,
                'confirmationMessage' => function ($entity, $label) {
                    return sprintf('Are you sure you want to %s this item?', strtolower($label));
                },
                'buttonStyle' => 'btn-default',
            ])
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

    public function all()
    {
        return $this->actions;
    }

    /**
     * @param object $entity
     */
    public function forEntity($entity)
    {
        $allowed = [];
        foreach ($this->actions as $action) {
            if ($action->isGranted($entity)) {
                $allowed[] = $action;
            }
        }

        return $allowed;
    }

    /**
     * @param AdminRequest $request
     */
    public function forRequest(AdminRequest $request)
    {
        $allowed = [];
        foreach ($this->actions as $action) {
            if ($action->isAvailable($request)) {
                $allowed[] = $action;
            }
        }

        return $allowed;
    }
}
