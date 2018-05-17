<?php

namespace Perform\BaseBundle\Crud;

use Perform\BaseBundle\Crud\CrudNotFoundException;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Perform\BaseBundle\Doctrine\EntityResolver;

/**
 * CrudRegistry.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class CrudRegistry
{
    protected $container;
    protected $cruds = [];
    protected $aliases = [];
    protected $resolver;

    public function __construct(ContainerInterface $container, EntityResolver $resolver)
    {
        $this->container = $container;
        $this->resolver = $resolver;
    }

    /**
     * Register an admin service with the registry.
     *
     * The service is fetched lazily from the container for performance reasons.
     *
     * @param string $entity  the fully qualified class name of the entity
     * @param string $service the name of the service in the container
     */
    public function addCrud($entity, $service)
    {
        $this->cruds[$entity] = $service;
    }

    /**
     * Get the Crud instance for managing $entity.
     *
     * @param string $entity the full class name of the entity
     */
    public function getCrud($entity)
    {
        try {
            $class = $this->resolver->resolve($entity);
        } catch (\InvalidArgumentException $e) {
            throw new CrudNotFoundException('Crud not found, invalid argument.', 1, $e);
        }

        if (isset($this->cruds[$class])) {
            return $this->container->get($this->cruds[$class]);
        }

        throw new CrudNotFoundException(sprintf('Crud not found for entity "%s"', $class));
    }

    /**
     * Return true if the given entity or class has an crud.
     *
     * @return bool
     */
    public function hasCrud($entity)
    {
        try {
            return isset($this->cruds[$this->resolver->resolve($entity)]);
        } catch (\InvalidArgumentException $e) {
            return false;
        }
    }

    /**
     * @return array
     */
    public function all()
    {
        return $this->cruds;
    }
}
