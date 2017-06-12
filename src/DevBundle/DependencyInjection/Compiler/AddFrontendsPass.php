<?php

namespace Perform\DevBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * AddFrontendsPass
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class AddFrontendsPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $definition = $container->getDefinition('perform_dev.frontend_registry');

        foreach ($container->findTaggedServiceIds('perform_dev.frontend') as $service => $tag) {
            $definition->addMethodCall('add', [new Reference($service)]);
        }
    }
}
