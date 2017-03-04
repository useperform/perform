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
        $services = [];
        foreach ($container->findTaggedServiceIds('perform_base.action') as $service => $tag) {
            if (!isset($tag[0]['alias'])) {
                throw new \InvalidArgumentException(sprintf('The service "%s" tagged with "perform_base.action" must set the "alias" option in the tag.', $service));
            }
            $alias = $tag[0]['alias'];
            $services[$alias] = $service;
        }

        $definition = $container->getDefinition('perform_base.action_registry');
        $definition->replaceArgument(1, $services);
    }
}
