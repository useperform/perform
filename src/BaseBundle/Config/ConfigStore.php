<?php

namespace Perform\BaseBundle\Config;

use Perform\BaseBundle\Doctrine\EntityResolver;
use Perform\BaseBundle\Admin\AdminRegistry;
use Perform\BaseBundle\Type\TypeRegistry;
use Perform\BaseBundle\Action\ActionRegistry;
use Perform\BaseBundle\Config\TypeConfig;
use Perform\BaseBundle\Config\FilterConfig;
use Perform\BaseBundle\Config\ActionConfig;

/**
 * ConfigStore creates and stores a single instance of the different
 * config classes for each entity.
 *
 * Config classes are configured by the entity admin, and may also be
 * overridden by configuration passed to the ConfigStore.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class ConfigStore implements ConfigStoreInterface
{
    protected $resolver;
    protected $adminRegistry;
    protected $typeRegistry;
    protected $actionRegistry;
    protected $override;

    protected $typeConfigs = [];
    protected $filterConfigs = [];
    protected $actionConfigs = [];

    public function __construct(EntityResolver $resolver, AdminRegistry $adminRegistry, TypeRegistry $typeRegistry, ActionRegistry $actionRegistry, array $override = [])
    {
        $this->resolver = $resolver;
        $this->adminRegistry = $adminRegistry;
        $this->typeRegistry = $typeRegistry;
        $this->actionRegistry = $actionRegistry;
        $this->override = $override;
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
        $class = $this->resolver->resolve($entity);
        if (!isset($this->typeConfigs[$class])) {
            $typeConfig = new TypeConfig($this->typeRegistry);
            $this->adminRegistry->getAdmin($class)->configureTypes($typeConfig);

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
     * Get the ActionConfig for an entity. The action config may include
     * overrides from application configuration.
     *
     * @param string|object $entity
     *
     * @return ActionConfig
     */
    public function getActionConfig($entity)
    {
        $class = $this->resolver->resolve($entity);
        if (!isset($this->actionConfigs[$class])) {
            $this->actionConfigs[$class] = new ActionConfig($this->actionRegistry);
            $this->adminRegistry->getAdmin($class)->configureActions($this->actionConfigs[$class]);
        }

        return $this->actionConfigs[$class];
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
        $class = $this->resolver->resolve($entity);
        if (!isset($this->filterConfigs[$class])) {
            $this->filterConfigs[$class] = new FilterConfig();
            $this->adminRegistry->getAdmin($class)->configureFilters($this->filterConfigs[$class]);
        }

        return $this->filterConfigs[$class];
    }
}
