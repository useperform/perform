<?php

namespace Perform\BaseBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Perform\BaseBundle\Crud\InvalidCrudException;

/**
 * Register Crud services.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class CrudPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $definition = $container->getDefinition('perform_base.crud.registry');
        $entityAliases = $container->getParameter('perform_base.entity_aliases');
        $extendedEntities = $container->getParameter('perform_base.extended_entities');
        $cruds = [];

        foreach ($container->findTaggedServiceIds('perform_base.crud') as $service => $tag) {
            if (!isset($tag[0]['entity'])) {
                throw new \InvalidArgumentException(sprintf('The service "%s" tagged with "perform_base.crud" must set the "entity" option in the tag.', $service));
            }
            $entityAlias = $tag[0]['entity'];
            $entityClass = isset($entityAliases[$entityAlias]) ? $entityAliases[$entityAlias] : $entityAlias;
            if (!class_exists($entityClass)) {
                throw new InvalidCrudException(sprintf('The crud service "%s" references an unknown entity class "%s".', $service, $entityClass));
            }

            $cruds[$entityClass] = $service;
        }

        foreach ($cruds as $entityClass => $service) {
            //entity is extended, register the child instead
            if (isset($extendedEntities[$entityClass])) {
                //if the child has no crud, register the parent crud
                //if the child has a crud, register the child crud
                $childClass = $extendedEntities[$entityClass];
                $service = isset($cruds[$childClass]) ? $cruds[$childClass] : $service;

                $definition->addMethodCall('add', [$childClass, $service]);
                continue;
            }

            //entity is a child, covered above
            if (in_array($entityClass, $extendedEntities)) {
                continue;
            }

            //normal entity
            $definition->addMethodCall('add', [$entityClass, $service]);
        }
    }
}
