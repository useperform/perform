<?php

namespace Perform\MediaBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\Config\Definition\Processor;
use Perform\MediaBundle\DependencyInjection\Configuration;
use Perform\MediaBundle\Exception\PluginNotFoundException;

/**
 * RegisterFilePluginsPass
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class RegisterFilePluginsPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $registry = $container->getDefinition('admin_media.plugin.registry');
        $pluginServices = [];

        foreach ($container->findTaggedServiceIds('admin_media.file_plugin') as $service => $tag) {
            if (!isset($tag[0]['pluginName'])) {
                throw new \InvalidArgumentException(sprintf('The service %s tagged with "admin_media.file_plugin" must set the "pluginName" option in the tag.', $service));
            }
            $pluginServices[$tag[0]['pluginName']] = $service;
        }

        foreach ($container->getParameter('admin_media.plugins') as $plugin) {
            if (!isset($pluginServices[$plugin])) {
                throw new PluginNotFoundException(sprintf('"%s" media plugin not found.', $plugin));
            }

            $registry->addMethodCall('addPlugin', [new Reference($pluginServices[$plugin])]);
        }
    }
}
