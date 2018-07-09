<?php

namespace Perform\BaseBundle\FieldType;

use Perform\BaseBundle\DependencyInjection\LoopableServiceLocator;
use Perform\BaseBundle\Exception\FieldTypeNotFoundException;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class FieldTypeRegistry
{
    protected $locator;
    protected $resolvers = [];

    public function __construct(LoopableServiceLocator $locator)
    {
        $this->locator = $locator;
    }

    public function getType($name)
    {
        if (!$this->locator->has($name)) {
            throw new FieldTypeNotFoundException(sprintf('Entity field type not found: "%s"', $name));
        }

        return $this->locator->get($name);
    }

    /**
     * Get a configured OptionsResolver for a given field type.
     *
     * The resolver will be used to validate each of the options keys
     * when adding the field: 'options', 'listOptions', 'viewOptions',
     * 'createOptions', 'editOptions'.
     *
     * @return OptionsResolver
     */
    public function getOptionsResolver($name)
    {
        if (!isset($this->resolvers[$name])) {
            $resolver = new OptionsResolver();
            $resolver->setRequired('label');
            $resolver->setAllowedTypes('label', 'string');

            $this->getType($name)->configureOptions($resolver);
            $this->resolvers[$name] = $resolver;
        }

        return $this->resolvers[$name];
    }

    /**
     * Get all available types, indexed by their aliases.
     *
     * @return TypeInterface[]
     */
    public function getAll()
    {
        $types = [];
        foreach ($this->locator as $alias => $type) {
            $types[$alias] = $type;
        }

        return $types;
    }
}
