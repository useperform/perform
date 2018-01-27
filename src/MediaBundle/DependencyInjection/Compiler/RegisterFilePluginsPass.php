<?php

namespace Perform\MediaBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\ServiceLocator;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class RegisterFilePluginsPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $locator = new Definition(ServiceLocator::class);
        $locator->setPublic(false)
            ->addTag('container.service_locator');

        $plugins = [];
        foreach ($container->findTaggedServiceIds('perform_media.file_plugin') as $service => $tag) {
            if (!isset($tag[0]['pluginName'])) {
                throw new \InvalidArgumentException(sprintf('The service %s tagged with "perform_media.file_plugin" must set the "pluginName" option in the tag.', $service));
            }
            $plugins[$tag[0]['pluginName']] = new Reference($service);
        }

        $locator->setArguments([$plugins]);

        $container->getDefinition('perform_media.plugin.registry')
            ->setArgument(0, $locator);
    }
}
