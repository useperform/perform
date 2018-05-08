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
    const PARAM_ENTRYPOINTS = 'perform_base.assets.entrypoints';
    const PARAM_EXTRA_SASS = 'perform_base.assets.extra_sass';
    const PARAM_EXTRA_JS = 'perform_base.assets.extra_js';
    const PARAM_NPM_CONFIGS = 'perform_base.assets.npm_configs';

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

    /**
     * Add a standalone asset entry point.
     *
     * @param string|array $path
     */
    public static function addEntryPoint(ContainerBuilder $container, $name, $path)
    {
        $path = (array) $path;
        $existing = $container->hasParameter(self::PARAM_ENTRYPOINTS) ? $container->getParameter(self::PARAM_ENTRYPOINTS) : [];

        if (isset($existing[$name])) {
            throw new \Exception(sprintf('The asset entry point "%s" has already been registered.', $name));
        }

        $container->setParameter(self::PARAM_ENTRYPOINTS, array_merge($existing, [
            $name => $path,
        ]));
    }

    /**
     * Add a file to include in the perform.js build.
     *
     * Exported functions from the imported file will be added to the
     * global window.Perform object under the supplied name.
     *
     * e.g. 'myApp' becomes 'Perform.myApp'
     *
     * @param string $name   The name to expose the functions under
     * @param string $import The file to import. It should export a javascript object containing functions to expose.
     */
    public static function addExtraJavascript(ContainerBuilder $container, $name, $import)
    {
        $existing = $container->hasParameter(self::PARAM_EXTRA_JS) ? $container->getParameter(self::PARAM_EXTRA_JS) : [];

        if (isset($existing[$name])) {
            throw new \Exception(sprintf('Extra javascript has already been registered under the "%s" name.', $name));
        }

        $container->setParameter(self::PARAM_EXTRA_JS, array_merge($existing, [
            $name => $import,
        ]));
    }

    /**
     * Add a file to include in the perform.scss build.
     *
     * You'll most likely want to prefix the path with a tilde (~) to
     * import from a namespace.
     *
     * @param ContainerBuilder $container
     * @param string           $path      e.g. '~my-namespace/custom.scss'
     */
    public static function addExtraSass(ContainerBuilder $container, $path)
    {
        $existing = $container->hasParameter(self::PARAM_EXTRA_SASS) ? $container->getParameter(self::PARAM_EXTRA_SASS) : [];
        $container->setParameter(self::PARAM_EXTRA_SASS, array_merge($existing, [$path]));
    }

    /**
     * Add a package.json file to the list of npm dependencies to be merged.
     *
     * @param ContainerBuilder $container
     * @param string           $file
     */
    public static function addNpmConfig(ContainerBuilder $container, $file)
    {
        $existing = $container->hasParameter(self::PARAM_NPM_CONFIGS) ? $container->getParameter(self::PARAM_NPM_CONFIGS) : [];
        $container->setParameter(self::PARAM_NPM_CONFIGS, array_merge($existing, [$file]));
    }
}
