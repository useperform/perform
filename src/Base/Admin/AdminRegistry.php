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

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @param string $entityClass in the style of doctrine, e.g. AdminBaseBundle:User
     * @param string $service     the name of the service in the container
     */
    public function addAdmin($entityClass, $service)
    {
        $this->admins[$entityClass] = $service;
    }

    /**
     * Get the Admin instance for managing $entityClass.
     */
    public function getAdmin($entityClass)
    {
        if (isset($this->admins[$entityClass])) {
            return $this->container->get($this->admins[$entityClass]);
        }

        throw new AdminNotFoundException(sprintf('Admin not found for entity "%s"', $entityClass));
    }
}
