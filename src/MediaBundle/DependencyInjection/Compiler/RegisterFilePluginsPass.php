<?php

namespace Admin\MediaBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\Config\Definition\Processor;
use Admin\MediaBundle\DependencyInjection\Configuration;

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

        // is there a better way to get the resolved configuration without having to do it manually?
        $processor = new Processor();
        $config = $processor->processConfiguration(new Configuration(), $container->getExtensionConfig('admin_media'));
        $plugins = $config['plugins'];

        foreach ($container->findTaggedServiceIds('admin_media.file_plugin') as $service => $tag) {
            if (!isset($tag[0]['pluginName'])) {
                throw new \InvalidArgumentException(sprintf('The service %s tagged with "admin_media.file_plugin" must set the "pluginName" option in the tag.', $service));
            }
            if (!in_array($tag[0]['pluginName'], $plugins)) {
                continue;
            }

            $registry->addMethodCall('addPlugin', [new Reference($service)]);
        }
    }
}
