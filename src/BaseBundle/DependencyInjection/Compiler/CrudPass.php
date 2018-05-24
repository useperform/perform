<?php

namespace Perform\BaseBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Perform\BaseBundle\Crud\InvalidCrudException;
use Symfony\Component\DependencyInjection\Reference;
use Perform\BaseBundle\DependencyInjection\LoopableServiceLocator;

/**
 * Register Crud services.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class CrudPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $crudNames = [];
        $crudEntityMap = [];

        foreach ($container->findTaggedServiceIds('perform_base.crud') as $service => $tags) {
            $crudClass = $container->getDefinition($service)->getClass();
            $entityClass = $crudClass::getEntityClass();
            if (!class_exists($entityClass)) {
                throw new InvalidCrudException(sprintf('The crud service "%s" references an unknown entity class "%s".', $service, $entityClass));
            }

            if (!isset($crudEntityMap[$entityClass])) {
                $crudEntityMap[$entityClass] = [];
            }

            foreach ($tags as $tag) {
                $crudName = isset($tag['crud_name']) ? $tag['crud_name'] : $this->createCrudName($service);

                $crudNames[$crudName] = new Reference($service);
                $crudEntityMap[$entityClass][] = $crudName;
            }
        }

        $container->getDefinition('perform_base.crud.registry')
            ->setArgument(0, LoopableServiceLocator::createDefinition($crudNames))
            ->setArgument(2, $crudEntityMap);
    }

    private function createCrudName($service)
    {
        // generate a sensible name from the class or service definition
        return $service;
    }
}
