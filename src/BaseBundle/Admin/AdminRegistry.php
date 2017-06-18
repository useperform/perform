<?php

namespace Perform\BaseBundle\Admin;

use Perform\BaseBundle\Exception\AdminNotFoundException;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Perform\BaseBundle\Doctrine\EntityResolver;

/**
 * AdminRegistry.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class AdminRegistry
{
    protected $container;
    protected $admins = [];
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
    public function addAdmin($entity, $service)
    {
        $this->admins[$entity] = $service;
    }

    /**
     * Get the Admin instance for managing $entity.
     *
     * @param string $entity the full class name of the entity
     */
    public function getAdmin($entity)
    {
        $class = $this->resolver->resolve($entity);
        if (isset($this->admins[$class])) {
            return $this->container->get($this->admins[$class]);
        }

        throw new AdminNotFoundException(sprintf('Admin not found for entity "%s"', $class));
    }

    /**
     * @return array
     */
    public function getAdmins()
    {
        return $this->admins;
    }
}
