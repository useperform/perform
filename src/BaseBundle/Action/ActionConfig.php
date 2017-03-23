<?php

namespace Perform\BaseBundle\Action;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Perform\BaseBundle\Util\StringUtil;

/**
 * ActionConfig.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class ActionConfig
{
    protected $registry;
    protected $resolver;
    protected $actions = [];

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
                'confirmationMessage' => function($entity, $label) {
                    return sprintf('Are you sure you want to %s this item?', strtolower($label));
                },
                'buttonStyle' => 'btn-default',
            ])
            ->setAllowedTypes('confirmationMessage', ['string', 'Closure'])
            ->setAllowedTypes('confirmationRequired', ['bool', 'Closure'])
            ->setAllowedTypes('buttonStyle', 'string')
            ;
    }

    public function add($name, array $options = [])
    {
        $action = $this->registry->getAction($name);
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

    public function get($name)
    {
        return isset($this->actions[$name]) ? $this->actions[$name] : null;
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
}
