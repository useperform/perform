<?php

namespace Perform\BaseBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Perform\BaseBundle\Crud\InvalidCrudException;
use Symfony\Component\DependencyInjection\Reference;
use Perform\BaseBundle\DependencyInjection\LoopableServiceLocator;
use Perform\BaseBundle\Crud\DuplicateCrudException;
use Symfony\Component\DependencyInjection\Definition;
use Perform\BaseBundle\Util\StringUtil;

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
        $crudRoutes = [];

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
                if (!isset($tag['crud_name'])) {
                    $tag['crud_name'] = $this->createCrudName($container->getDefinition($service));
                }
                $crudName = $tag['crud_name'];

                if (isset($crudNames[$crudName])) {
                    throw new DuplicateCrudException(sprintf('A crud with the name "%s" is already defined. You should explicitly set the "crud_name" attribute of the "perform_base.crud" tag on the "%s" service to something not in the existing list: "%s".', $crudName, $service, implode('", "', array_keys($crudNames))));
                }

                $crudNames[$crudName] = new Reference($service);
                $crudEntityMap[$entityClass][] = $crudName;
                $crudRoutes[$crudName] = $this->getRouteOptionsFromTag($tag);
            }
        }

        $container->getDefinition('perform_base.crud.registry')
            ->setArgument(2, LoopableServiceLocator::createDefinition($crudNames))
            ->setArgument(3, $crudEntityMap);

        $container->getDefinition('perform_base.routing.crud_loader')
            ->setArgument(1, $crudRoutes);

        $container->getDefinition('perform_base.routing.crud_generator')
            ->setArgument(1, $crudRoutes);
    }

    private function createCrudName(Definition $definition)
    {
        return strtolower(preg_replace('/([A-Z])/', '_\1', lcfirst(StringUtil::classBasename($definition->getClass(), 'Crud'))));
    }

    private function getRouteOptionsFromTag(array $tag)
    {
        $options = [];

        $crudName = $tag['crud_name'];
        $options['route_name_prefix'] = isset($tag['route_name_prefix']) ?
                                      $tag['route_name_prefix'] :
                                      $this->createRouteNamePrefix($crudName);

        $defaultContexts = [
            'list' => '/',
            'view' => '/view/{id}',
            'create' => '/create',
            'edit' => '/edit/{id}',
        ];
        $unsetCount = 0;

        foreach ($defaultContexts as $context => $urlFragment) {
            if (!isset($tag[$context.'_context'])) {
                ++$unsetCount;
                continue;
            }
            $options['contexts'][$context] = $tag[$context.'_context'];
        }

        // if no contexts are defined, load them all.
        if ($unsetCount === count($defaultContexts)) {
            $options['contexts'] = $defaultContexts;
        }

        return $options;
    }

    private function createRouteNamePrefix($crudName)
    {
        return preg_replace('/([^_a-z0-9])/', '_', strtolower($crudName)).'_';
    }
}
