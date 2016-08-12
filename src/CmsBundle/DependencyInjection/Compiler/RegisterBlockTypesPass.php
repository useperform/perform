<?php

namespace Admin\CmsBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * RegisterBlockTypesPass
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class RegisterBlockTypesPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $registry = $container->getDefinition('admin_cms.block_type_registry');

        $availableTypes = [];

        foreach ($container->findTaggedServiceIds('admin_cms.block_type') as $service => $tag) {
            if (!isset($tag[0]['type'])) {
                throw new \InvalidArgumentException(sprintf('The service "%s" tagged with "admin_cms.block_type" must set the "type" option in the tag.', $service));
            }

            $availableTypes[$tag[0]['type']] = $service;
        }

        $requestedTypes = $container->getParameter('admin_cms.block_types');
        $usedTypes = [];
        foreach ($requestedTypes as $type) {
            if (!isset($availableTypes[$type])) {
                throw new \InvalidArgumentException(sprintf('Unknown cms block type "%s"', $type));
            }
            $usedTypes[$type] = $availableTypes[$type];
        }

        foreach ($usedTypes as $type => $service) {
            $registry->addMethodCall('addType', [$type, new Reference($service)]);
        }

        foreach ($availableTypes as $service) {
            $container->getDefinition($service)->setPublic(false);
        }
    }

}
