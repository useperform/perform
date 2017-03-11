<?php

namespace Perform\BaseBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Setup actions.
 **/
class ConfigureActionsPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $definition = $container->getDefinition('perform_base.action_registry');

        $aliases = $container->getParameter('perform_base.entity_aliases');
        $extendedEntities = $container->getParameter('perform_base.extended_entities');

        foreach ($container->findTaggedServiceIds('perform_base.action') as $service => $tag) {
            foreach ($tag as $item) {
                if (!isset($item['alias'])) {
                    throw new \InvalidArgumentException(sprintf('The service "%s" tagged with "perform_base.action" must set the "alias" option in the tag.', $service));
                }
                $definition->addMethodCall('addAction', [$item['alias'], $service]);
            }
        }
    }
}
