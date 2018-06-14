<?php

namespace Perform\BaseBundle\Crud;

use Perform\BaseBundle\Doctrine\EntityResolver;
use Perform\BaseBundle\DependencyInjection\LoopableServiceLocator;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\MappingException;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class CrudRegistry
{
    protected $resolver;
    protected $em;
    protected $cruds;
    protected $crudEntityMap = [];

    public function __construct(EntityResolver $resolver, EntityManagerInterface $em, LoopableServiceLocator $cruds, array $crudEntityMap)
    {
        $this->resolver = $resolver;
        $this->em = $em;
        $this->cruds = $cruds;
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
            throw new CrudNotFoundException(sprintf('Crud service not found: "%s". Use the crud data collector panel or the perform:debug:crud command to see the available services.', $crudName));
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
            throw new DuplicateCrudException(sprintf('More than one crud service is available for entity class %s. You should explicitly fetch one of "%s" with CrudRegistry#get().', $entityClass, implode('", "', $names)));
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
        $entityClass = $this->resolver->resolve($entity);

        return isset($this->crudEntityMap[$entityClass]) ? $this->crudEntityMap[$entityClass] : [];
    }

    /**
     * Return true if the given entity has at least one crud service.
     *
     * @param mixed $entity
     *
     * @return true
     */
    public function hasForEntity($entity)
    {
        return count($this->getAllNamesForEntity($entity)) > 0;
    }

    /**
     * Get the crud name for an entity relation.
     *
     * For example, if a BlogPost had an 'author' property, the crud
     * name for an Author entity would be returned.
     */
    public function getNameForRelatedEntity($entity, $property)
    {
        $metadata = $this->em->getClassMetadata($this->resolver->resolve($entity));

        try {
            $relatedClass = $metadata->getAssociationMapping($property)['targetEntity'];

            return $this->getNameForEntity($relatedClass);
        } catch (MappingException $e) {
            $msg = sprintf('Entity field "%s" of class "%s" must be a doctrine association to be able to fetch a crud service for it.', $property, $metadata->name);
            throw new CrudNotFoundException($msg, 1, $e);
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
