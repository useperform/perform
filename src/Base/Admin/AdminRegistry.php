<?php

namespace Admin\Base\Admin;

use Admin\Base\Exception\AdminNotFoundException;
use Symfony\Component\DependencyInjection\ContainerInterface;

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

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * Register an admin service with the registry.
     *
     * For performance reasons, the service is fetched lazily, and
     * alias and class name are required in advance.
     *
     * @param string $entity      in the style of doctrine, e.g. AdminBaseBundle:User
     * @param string $entityClass the fully qualified class name of the entity
     * @param string $service     the name of the service in the container
     */
    public function addAdmin($entity, $entityClass, $service)
    {
        if (strpos($entity, '\\') !== false || strpos($entity, ':') === false) {
            throw new \InvalidArgumentException('An admin service must be registered with the entity alias, e.g. AdminBaseBundle:User.');
        }

        $this->admins[$entity] = $service;
        $this->admins[$entityClass] = $service;
    }

    /**
     * Get the Admin instance for managing $entity.
     *
     * @param string $entity in the style of doctrine (e.g. AdminBaseBundle:User), or the full class name of the entity
     */
    public function getAdmin($entity)
    {
        if (isset($this->admins[$entity])) {
            return $this->container->get($this->admins[$entity]);
        }

        throw new AdminNotFoundException(sprintf('Admin not found for entity "%s"', $entity));
    }

    /**
     * Get the Admin instance for managing an entity.
     */
    public function getAdminForEntity($entity)
    {
        return $this->getAdmin(get_class($entity));
    }
}
