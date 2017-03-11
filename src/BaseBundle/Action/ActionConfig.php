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
            ;
    }

    public function add($name, array $options = [])
    {
        foreach (['label', 'batchLabel'] as $key) {
            if (!isset($options[$key])) {
                $options[$key] = StringUtil::sensible($name);
            }
        }
        $options = $this->resolver->resolve($options);

        if (!$options['label'] instanceof \Closure) {
            $label = $options['label'];
            $options['label'] = function ($entity) use ($label) {
                return $label;
            };
        }
        if (!$options['batchLabel'] instanceof \Closure) {
            $label = $options['batchLabel'];
            $options['batchLabel'] = function () use ($label) {
                return $label;
            };
        }

        $this->actions[$name] = new ConfiguredAction($name, $this->registry->getAction($name), $options['label'], $options['batchLabel']);

        return $this;
    }

    public function all()
    {
        return $this->actions;
    }
}
