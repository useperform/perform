<?php

namespace Perform\BaseBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Add entries to various container parameters related to assets.
 *
 * Applications don't need to use this, they can define
 * perform_base.assets configuration nodes.
 *
 * These helpers are available to bundle extensions so they don't have
 * to implement PrependExtensionInterface to add to these nodes.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class Assets
{
    const PARAM_NAMESPACES = 'perform_base.assets.namespaces';

    /**
     * Add an asset namespace to be used for `resolve.alias` in the webpack builds.
     */
    public static function addNamespace(ContainerBuilder $container, $namespace, $directory)
    {
        $existing = $container->hasParameter(self::PARAM_NAMESPACES) ? $container->getParameter(self::PARAM_NAMESPACES) : [];

        if (isset($existing[$namespace])) {
            throw new \Exception(sprintf('The asset namespace "%s" has already been registered.', $namespace));
        }

        $container->setParameter(self::PARAM_NAMESPACES, array_merge($existing, [
            $namespace => $directory,
        ]));
    }
}
