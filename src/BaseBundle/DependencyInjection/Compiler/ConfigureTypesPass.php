<?php

namespace Perform\BaseBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Setup type services.
 **/
class ConfigureTypesPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $definition = $container->getDefinition('perform_base.type_registry');

        foreach ($container->findTaggedServiceIds('perform_base.type') as $service => $tag) {
            foreach ($tag as $item) {
                if (!isset($item['alias'])) {
                    throw new \InvalidArgumentException(sprintf('The service "%s" tagged with "perform_base.type" must set the "alias" option in the tag.', $service));
                }
                $definition->addMethodCall('addTypeService', [$item['alias'], $service]);
            }
        }
    }
}
