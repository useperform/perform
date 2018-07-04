<?php

namespace Perform\BaseBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Perform\BaseBundle\DependencyInjection\LoopableServiceLocator;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class InstallersPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $installers = [];
        foreach ($container->findTaggedServiceIds('perform_base.installer') as $service => $tag) {
            $installers[] = new Reference($service);
        }
        $container->getDefinition('Perform\BaseBundle\Command\InstallCommand')
            ->setArgument(0, LoopableServiceLocator::createDefinition($installers));
    }
}
