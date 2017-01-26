<?php

namespace Perform\BaseBundle\Type;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Perform\BaseBundle\Admin\AdminRegistry;
use Perform\BaseBundle\Filter\FilterConfig;

/**
 * EntityTypeConfig enables overriding admin type configuration, e.g. from
 * container configuration.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class EntityTypeConfig
{
    public function __construct(AdminRegistry $registry, array $override)
    {
        $this->registry = $registry;
        $this->override = $override;
    }

    public function getEntityTypeConfig($entityName)
    {
        $typeConfig = new TypeConfig();
        $this->registry->getAdmin($entityName)->configureTypes($typeConfig);

        if (isset($this->override[$entityName]['types'])) {
            foreach ($this->override[$entityName]['types'] as $field => $config) {
                $typeConfig->add($field, $config);
            }
        }

        return $typeConfig;
    }

    public function getEntityFilterConfig($entityName)
    {
        $config = new FilterConfig();
        $this->registry->getAdmin($entityName)->configureFilters($config);

        return $config;
    }
}
