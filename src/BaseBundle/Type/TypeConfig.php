<?php

namespace Perform\BaseBundle\Type;

use Perform\BaseBundle\Type\TypeRegistry;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * TypeConfig
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class TypeConfig
{
    const CONTEXT_LIST = 'list';
    const CONTEXT_VIEW = 'view';
    const CONTEXT_CREATE = 'create';
    const CONTEXT_EDIT = 'edit';
    protected static $contextKeys = [
        'list' => 'listOptions',
        'view' => 'viewOptions',
        'create' => 'createOptions',
        'edit' => 'editOptions',
    ];

    protected $resolver;
    protected $types = [];

    public function __construct()
    {
        $this->resolver = new OptionsResolver();
        $optionKeys = ['options', 'listOptions', 'viewOptions', 'createOptions', 'editOptions'];

        $this->resolver
            ->setRequired(['type'])
            ->setDefaults([
                'contexts' => [
                    static::CONTEXT_LIST,
                    static::CONTEXT_VIEW,
                    static::CONTEXT_CREATE,
                    static::CONTEXT_EDIT,
                ],
                'options' => [],
            ])
            ->setAllowedTypes('contexts', 'array')
            ->setDefined($optionKeys);
        foreach ($optionKeys as $key) {
            $this->resolver->setAllowedTypes($key, 'array');
        }
    }

    public function getTypes($context)
    {
        $types = [];
        foreach ($this->types as $field => $config) {
            if (!in_array($context, $config['contexts'])) {
                continue;
            }

            $mergeKey = static::$contextKeys[$context];
            $options = isset($config[$mergeKey]) ?
                     array_merge($config['options'], $config[$mergeKey]) :
                     $config['options'];

            $types[$field] = [
                'type' => $config['type'],
                'options' => $options,
            ];
        }

        return $types;
    }

    public function add($name, array $options)
    {
        $this->types[$name] = $this->resolver->resolve($options);

        return $this;
    }
}
