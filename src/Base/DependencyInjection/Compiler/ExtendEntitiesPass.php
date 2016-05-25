<?php

namespace Admin\Base\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Register a yaml doctrine loader that will set all extended entities as mapped
 * superclasses.
 **/
class ExtendEntitiesPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $definition = $container->getDefinition('doctrine.orm.default_yml_metadata_driver');
        $definition->setClass('Admin\Base\Doctrine\ExtendedYamlDriver');
        $definition->addArgument('.orm.yml');
        $definition->addArgument($container->getParameter('admin_base.extended_entities'));
    }
}
