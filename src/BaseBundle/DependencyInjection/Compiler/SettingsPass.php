<?php

namespace Perform\BaseBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\Definition;
use Perform\BaseBundle\Settings\Manager\TraceableManager;
use Perform\BaseBundle\Util\ReflectionUtil;
use Perform\BaseBundle\Settings\Manager\WriteableSettingsManagerInterface;
use Perform\BaseBundle\Settings\Manager\CacheableManager;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class SettingsPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $this->configurePanels($container);
        $this->configureManager($container);
    }

    private function configurePanels(ContainerBuilder $container)
    {
        $services = [];
        foreach ($container->findTaggedServiceIds('perform_base.settings_panel') as $service => $tag) {
            if (!isset($tag[0]['alias'])) {
                throw new \InvalidArgumentException(sprintf('The service %s tagged with "perform_base.settings_panel" must set the "alias" option in the tag.', $service));
            }
            $name = $tag[0]['alias'];
            $services[$name] = new Reference($service);

            $container->getDefinition($service)->setPublic(false);
        }

        $definition = $container->getDefinition('perform_base.settings_panel_registry');
        $definition->replaceArgument(1, $services);
    }

    private function configureManager(ContainerBuilder $container)
    {
        $managerService = 'perform_base.settings_manager';
        $manager = $container->findDefinition($managerService);

        $innerManagerClass = $this->getInnerManagerClass($container, $manager);

        if (ReflectionUtil::implementsInterface($innerManagerClass, WriteableSettingsManagerInterface::class)) {
            $container->log($this, sprintf('Registering %s for autowiring %s.', $innerManagerClass, WriteableSettingsManagerInterface::class));
            $container->setAlias(WriteableSettingsManagerInterface::class, $managerService);
        } else {
            $container->log($this, sprintf("Not registering %s for autowiring %s, as it doesn't implement the interface.", $innerManagerClass, WriteableSettingsManagerInterface::class));
        }

        if ($container->hasDefinition('profiler')) {
            $traceableManager = new Definition(TraceableManager::class);
            $traceableManager->setArgument(0, $manager);
            $container->setDefinition($managerService, $traceableManager);
        } else {
            $container->removeDefinition('perform_base.data_collector.settings');
        }
    }

    private function getInnerManagerClass(ContainerBuilder $container, Definition $manager)
    {
        if ($manager->getClass() === CacheableManager::class) {
            $firstArg = $manager->getArgument(0);

            return $firstArg instanceof Definition ?
                $firstArg->getClass() :
                $container->findDefinition($firstArg)->getClass();
        }

        return $manager->getClass();
    }
}
