<?php

namespace Perform\BaseBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Helpers for other bundles to configure doctrine functionality
 * without implementing prepend extension interface to set
 * perform_base configuration.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class Doctrine
{
    const PARAM_RESOLVED = 'perform_base.resolved_entities';
    const PARAM_RESOLVED_CONFIG = 'perform_base.resolved_entities_config';
    const PARAM_RESOLVED_DEFAULTS = 'perform_base.resolved_entities_defaults';
    const PARAM_EXTRA_MAPPINGS = 'perform_base.extra_doctrine_mappings';

    /**
     * @param ContainerBuilder $container
     * @param string $interface
     * @param string $class
     */
    public static function registerDefaultImplementation(ContainerBuilder $container, $interface, $class)
    {
        $param = $container->hasParameter(self::PARAM_RESOLVED_DEFAULTS) ?
                  $container->getParameter(self::PARAM_RESOLVED_DEFAULTS) : [];
        $param[$interface] = $class;
        $container->setParameter(self::PARAM_RESOLVED_DEFAULTS, $param);
    }

    /**
     * @param ContainerBuilder $container
     * @param string $entityClass
     * @param string $mappingFile
     */
    public static function addExtraMapping(ContainerBuilder $container, $entityClass, $mappingFile)
    {
        $param = $container->hasParameter(self::PARAM_EXTRA_MAPPINGS) ?
                  $container->getParameter(self::PARAM_EXTRA_MAPPINGS) : [];
        $param[] = [
            $entityClass,
            $mappingFile,
        ];
        $container->setParameter(self::PARAM_EXTRA_MAPPINGS, $param);
    }
}
