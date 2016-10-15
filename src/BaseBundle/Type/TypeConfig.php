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

    protected $resolver;
    protected $types = [];

    public function __construct()
    {
        $this->resolver = new OptionsResolver();
        $this->resolver
            ->setRequired(['type'])
            ->setDefaults([
                'contexts' => [
                    static::CONTEXT_LIST,
                    static::CONTEXT_VIEW,
                    static::CONTEXT_CREATE,
                    static::CONTEXT_EDIT,
                ]
            ])
            ->setAllowedTypes('contexts', 'array');
    }

    public function getTypes($context)
    {
        $types = [];
        foreach ($this->types as $field => $options) {
            if (!in_array($context, $options['contexts'])) {
                continue;
            }

            $types[$field] = $options;
        }

        return $types;
    }

    public function add($name, array $options)
    {
        $this->types[$name] = $this->resolver->resolve($options);

        return $this;
    }
}
