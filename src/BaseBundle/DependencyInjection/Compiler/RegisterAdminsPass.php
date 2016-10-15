<?php

namespace Perform\BaseBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Register admins automatically.
 **/
class RegisterAdminsPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $definition = $container->getDefinition('perform_base.admin.registry');
        $entityAliases = $container->getParameter('perform_base.entity_aliases');
        $extendedAliases = $container->getParameter('perform_base.extended_entity_aliases');
        $admins = [];

        foreach ($container->findTaggedServiceIds('perform_base.admin') as $service => $tag) {
            if (!isset($tag[0]['entity'])) {
                throw new \InvalidArgumentException(sprintf('The service "%s" tagged with "perform_base.admin" must set the "entity" option in the tag.', $service));
            }
            $entityAlias = $tag[0]['entity'];
            if (!isset($entityAliases[$entityAlias])) {
                throw new \InvalidArgumentException(sprintf('The service "%s" references an unknown entity "%s".', $service, $entityAlias));
            }

            $admins[$entityAlias] = $service;
        }

        foreach ($admins as $entityAlias => $service) {
            $entityClass = $entityAliases[$entityAlias];

            //entity is extended
            if (isset($extendedAliases[$entityAlias])) {
                $childAlias = $extendedAliases[$entityAlias];
                //if the child has no admin, register both for the parent admin.
                //if the child has an admin, register both for the child admin.
                $service = isset($admins[$childAlias]) ? $admins[$childAlias] : $service;

                //parent
                $definition->addMethodCall('addAdmin', [$entityAlias, $entityClass, $service]);
                //child
                $definition->addMethodCall('addAdmin', [$childAlias, $entityAliases[$childAlias], $service]);
                continue;
            }

            //entity is extending, covered above
            if (in_array($entityAlias, $extendedAliases)) {
                continue;
            }

            //normal entity
            $definition->addMethodCall('addAdmin', [$entityAlias, $entityClass, $service]);
        }
    }
}
