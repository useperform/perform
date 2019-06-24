<?php

namespace Perform\DevBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Perform\DevBundle\Command\UpdateNpmDependenciesCommand;

class NpmDependenciesPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $deps = [];
        foreach (array_keys($container->findTaggedServiceIds('perform_dev.npm_dependencies')) as $service) {
            $deps[] = new Reference($service);
        }
        $container->getDefinition(UpdateNpmDependenciesCommand::class)
            ->setArgument(1, $deps);
    }
}
