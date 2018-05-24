<?php

namespace Perform\BaseBundle\Crud;

use Perform\BaseBundle\Doctrine\EntityResolver;
use Perform\BaseBundle\DependencyInjection\LoopableServiceLocator;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class CrudRegistry
{
    protected $cruds;
    protected $resolver;
    protected $crudEntityMap = [];

    public function __construct(LoopableServiceLocator $cruds, EntityResolver $resolver, array $crudEntityMap)
    {
        $this->cruds = $cruds;
        $this->resolver = $resolver;
        $this->crudEntityMap = $crudEntityMap;
    }

    /**
     * Get a crud service by name.
     *
     * @param string $crudName The name of the crud service
     */
    public function get($crudName)
    {
        if (!$this->cruds->has($crudName)) {
            throw new CrudNotFoundException(sprintf('Crud service not found: "%s"', $crudName));
        }

        return $this->cruds->get($crudName);
    }

    /**
     * Return true if the given crud exists.
     *
     * @return bool
     */
    public function has($crudName)
    {
        return $this->cruds->has($crudName);
    }

    /**
     * Get the crud name for managing $entity.
     *
     * An exception will be thrown if more than one crud exists for the given entity.
     *
     * @param mixed $entity
     *
     * @throws DuplicateCrudException When more than one crud is available for an entity
     */
    public function getNameForEntity($entity)
    {
        $names = $this->getAllNamesForEntity($entity);

        if (!isset($names[0])) {
            $entityClass = $this->resolver->resolve($entity);
            throw new CrudNotFoundException(sprintf('No crud service is available for entity class %s.', $entityClass));
        }

        if (count($names) > 1) {
            $entityClass = $this->resolver->resolve($entity);
            throw new DuplicateCrudException(sprintf('More than one crud service is available for entity class %s. You should find the crud service you want to use and explicitly fetch it with CrudRegistry#get().', $entityClass));
        }

        return $names[0];
    }

    /**
     * Get all the available crud names for an entity.
     *
     * @param mixed $entity
     */
    public function getAllNamesForEntity($entity)
    {
        try {
            $entityClass = $this->resolver->resolve($entity);
        } catch (\InvalidArgumentException $e) {
            throw new CrudNotFoundException('Crud not found, invalid argument.', 1, $e);
        }

        return isset($this->crudEntityMap[$entityClass]) ? $this->crudEntityMap[$entityClass] : [];
    }

    /**
     * Return true is the given entity has at least one crud service.
     *
     * @param mixed $entity
     *
     * @return true
     */
    public function hasForEntity($entity)
    {
        try {
            return count($this->getAllNamesForEntity($entity)) > 0;
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
