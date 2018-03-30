<?php

namespace Perform\RichContentBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Perform\RichContentBundle\BlockType\ImageBlockType;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class RegisterBlockTypesPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $registry = $container->getDefinition('perform_rich_content.block_type_registry');
        $this->removeUnavailableBlockTypes($container);

        $availableTypes = [];
        foreach ($container->findTaggedServiceIds('perform_rich_content.block_type') as $service => $tag) {
            if (!isset($tag[0]['type'])) {
                throw new \InvalidArgumentException(sprintf('The service "%s" tagged with "perform_rich_content.block_type" must set the "type" option in the tag.', $service));
            }

            $availableTypes[$tag[0]['type']] = $service;
        }

        $requestedTypes = $container->getParameter('perform_rich_content.block_types');
        if (empty($requestedTypes)) {
            $requestedTypes = array_keys($availableTypes);
        }

        $usedTypes = [];
        foreach ($requestedTypes as $type) {
            if (!isset($availableTypes[$type])) {
                throw new \InvalidArgumentException(sprintf('Unknown content block type "%s"', $type));
            }
            $usedTypes[$type] = $availableTypes[$type];
        }

        foreach ($usedTypes as $type => $service) {
            $registry->addMethodCall('add', [$type, new Reference($service)]);
        }
    }

    protected function removeUnavailableBlockTypes(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('perform_media.importer.file')) {
            $container->log($this, sprintf('Removing the %s block type; the MediaBundle is not registered.', ImageBlockType::class));
            $container->removeDefinition('perform_rich_content.block.image');
        }
    }
}
