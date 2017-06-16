<?php

namespace Perform\BaseBundle\Config;

use Perform\BaseBundle\Doctrine\EntityResolver;
use Perform\BaseBundle\Admin\AdminRegistry;
use Perform\BaseBundle\Type\TypeRegistry;
use Perform\BaseBundle\Type\TypeConfig;

/**
 * ConfigStore creates and stores a single instance of the different
 * config classes for each entity.
 *
 * Config classes are configured by the entity admin, and may also be
 * overridden by configuration passed to the ConfigStore.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class ConfigStore
{
    protected $typeConfigs = [];
    protected $resolver;
    protected $adminRegistry;
    protected $typeRegistry;
    protected $override;

    public function __construct(EntityResolver $resolver, AdminRegistry $adminRegistry, TypeRegistry $typeRegistry, array $override = [])
    {
        $this->resolver = $resolver;
        $this->adminRegistry = $adminRegistry;
        $this->typeRegistry = $typeRegistry;
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
}
