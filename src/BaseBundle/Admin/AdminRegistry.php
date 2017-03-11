<?php

namespace Perform\BaseBundle\Admin;

use Perform\BaseBundle\Exception\AdminNotFoundException;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Perform\BaseBundle\Type\TypeConfig;
use Perform\BaseBundle\Filter\FilterConfig;
use Perform\BaseBundle\Type\TypeRegistry;
use Perform\BaseBundle\Action\ActionRegistry;
use Perform\BaseBundle\Action\ActionConfig;

/**
 * AdminRegistry.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class AdminRegistry
{
    protected $container;
    protected $typeRegistry;
    protected $actionRegistry;
    protected $admins = [];
    protected $aliases = [];
    protected $override = [];
    protected $typeConfigs = [];
    protected $filterConfigs = [];
    protected $actionConfigs = [];

    public function __construct(ContainerInterface $container, TypeRegistry $typeRegistry, ActionRegistry $actionRegistry, array $override = [])
    {
        $this->container = $container;
        $this->typeRegistry = $typeRegistry;
        $this->actionRegistry = $actionRegistry;
        $this->override = $override;
    }

    /**
     * Register an admin service with the registry.
     *
     * For performance reasons, the service is fetched lazily, and
     * alias and class name are required in advance.
     *
     * @param string $entity      in the style of doctrine, e.g. PerformBaseBundle:User
     * @param string $entityClass the fully qualified class name of the entity
     * @param string $service     the name of the service in the container
     */
    public function addAdmin($entity, $entityClass, $service)
    {
        if (strpos($entity, '\\') !== false || strpos($entity, ':') === false) {
            throw new \InvalidArgumentException('An admin service must be registered with the entity alias, e.g. PerformBaseBundle:User.');
        }

        $this->admins[$entityClass] = $service;
        $this->aliases[$entity] = $entityClass;

        //an override may be indexed with the entity alias or entity class
        //if both are supplied, alias wins
        if (isset($this->override[$entity])) {
            $this->override[$entityClass] = $this->override[$entity];
            unset($this->override[$entity]);
        }
    }

    /**
     * Get the Admin instance for managing $entity.
     *
     * @param string $entity in the style of doctrine (e.g. PerformBaseBundle:User), or the full class name of the entity
     */
    public function getAdmin($entity)
    {
        $class = $this->resolveEntity($entity);
        if (isset($this->admins[$class])) {
            return $this->container->get($this->admins[$class]);
        }

        throw new AdminNotFoundException(sprintf('Admin not found for entity "%s"', $class));
    }

    /**
     * Get the fully qualified classname of an entity alias or object.
     *
     * @param string|object $entity
     *
     * @return string
     */
    public function resolveEntity($entity)
    {
        if (!is_string($entity)) {
            $entity = get_class($entity);
        }

        return isset($this->aliases[$entity]) ? $this->aliases[$entity] : $entity;
    }

    /**
     * @return array
     */
    public function getAdmins()
    {
        return $this->admins;
    }

    /**
     * Get the TypeConfig for an entity. The type config may include
     * overrides from application configuration.
     *
     * @param string|object $entity
     *
     * @return TypeConfig
     */
    public function getTypeConfig($entity)
    {
        $class = $this->resolveEntity($entity);
        if (!isset($this->typeConfigs[$class])) {
            $typeConfig = new TypeConfig($this->typeRegistry);
            $this->getAdmin($class)->configureTypes($typeConfig);

            if (isset($this->override[$class]['types'])) {
                foreach ($this->override[$class]['types'] as $field => $config) {
                    $typeConfig->add($field, $config);
                }
            }
            $this->typeConfigs[$class] = $typeConfig;
        }

        return $this->typeConfigs[$class];
    }

    /**
     * Get the FilterConfig for an entity. The filter config may include
     * overrides from application configuration.
     *
     * @param string|object $entity
     *
     * @return FilterConfig
     */
    public function getFilterConfig($entity)
    {
        $class = $this->resolveEntity($entity);
        if (!isset($this->filterConfigs[$class])) {
            $this->filterConfigs[$class] = new FilterConfig();
            $this->getAdmin($class)->configureFilters($this->filterConfigs[$class]);
        }

        return $this->filterConfigs[$class];
    }

    /**
     * Get the ActionConfig for an entity. The action config may include
     * overrides from application configuration.
     *
     * @param string|object $entity
     *
     * @return ActionConfig
     */
    public function getActionConfig($entity)
    {
        $class = $this->resolveEntity($entity);
        if (!isset($this->actionConfigs[$class])) {
            $this->actionConfigs[$class] = new ActionConfig($this->actionRegistry);
            $this->getAdmin($class)->configureActions($this->actionConfigs[$class]);
        }

        return $this->actionConfigs[$class];
    }
}
