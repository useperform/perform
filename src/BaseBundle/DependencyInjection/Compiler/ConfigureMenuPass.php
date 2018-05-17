<?php

namespace Perform\BaseBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class ConfigureMenuPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $services = [];
        foreach ($container->findTaggedServiceIds('perform_base.link_provider') as $service => $tag) {
            if (!isset($tag[0]['alias'])) {
                throw new \InvalidArgumentException(sprintf('The service %s tagged with "perform_base.link_provider" must set the "alias" option in the tag.', $service));
            }
            $services[$tag[0]['alias']] = $service;
        }

        $orderedServices = [];
        foreach ($container->getParameter('perform_base.menu_order') as $alias) {
            if (!isset($services[$alias])) {
                throw new \InvalidArgumentException(sprintf('Unknown menu alias "%s"', $alias));
            }
            $orderedServices[] = $services[$alias];
        }
        $services = empty($orderedServices) ? array_values($services) : $orderedServices;

        $definition = $container->getDefinition('perform_base.menu_builder');
        foreach ($services as $service) {
            $definition->addMethodCall('addLinkProvider', [new Reference($service)]);
        }
    }
}
