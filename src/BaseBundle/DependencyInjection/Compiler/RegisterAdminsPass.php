<?php

namespace Perform\BaseBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Perform\BaseBundle\Exception\InvalidAdminException;

/**
 * Register admins automatically.
 **/
class RegisterAdminsPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $definition = $container->getDefinition('perform_base.admin.registry');
        $entityAliases = $container->getParameter('perform_base.entity_aliases');
        $extendedEntities = $container->getParameter('perform_base.extended_entities');
        $admins = [];

        foreach ($container->findTaggedServiceIds('perform_base.admin') as $service => $tag) {
            if (!isset($tag[0]['entity'])) {
                throw new \InvalidArgumentException(sprintf('The service "%s" tagged with "perform_base.admin" must set the "entity" option in the tag.', $service));
            }
            $entityAlias = $tag[0]['entity'];
            $entityClass = isset($entityAliases[$entityAlias]) ? $entityAliases[$entityAlias] : $entityAlias;
            if (!class_exists($entityClass)) {
                throw new InvalidAdminException(sprintf('The admin service "%s" references an unknown entity class "%s".', $service, $entityClass));
            }

            $admins[$entityClass] = $service;
        }

        foreach ($admins as $entityClass => $service) {
            //entity is extended, register the child instead
            if (isset($extendedEntities[$entityClass])) {
                //if the child has no admin, register the parent admin
                //if the child has an admin, register the child admin
                $childClass = $extendedEntities[$entityClass];
                $service = isset($admins[$childClass]) ? $admins[$childClass] : $service;

                $definition->addMethodCall('addAdmin', [$childClass, $service]);
                continue;
            }

            //entity is a child, covered above
            if (in_array($entityClass, $extendedEntities)) {
                continue;
            }

            //normal entity
            $definition->addMethodCall('addAdmin', [$entityClass, $service]);
        }
    }
}
