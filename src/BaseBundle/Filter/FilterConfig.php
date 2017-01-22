<?php

namespace Perform\BaseBundle\Filter;

use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * FilterConfig.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class FilterConfig
{
    protected $resolver;
    protected $filters = [];

    public function __construct()
    {
        $this->resolver = new OptionsResolver();
        $this->resolver
            ->setRequired(['query'])
            ->setAllowedTypes('query', 'callable');
    }

    public function getFilter($name)
    {
        return isset($this->filters[$name]) ? $this->filters[$name] : null;
    }

    public function getFilters()
    {
        return $this->filters;
    }

    public function add($name, array $options)
    {
        $this->filters[$name] = $this->resolver->resolve($options);

        return $this;
    }
}
