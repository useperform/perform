<?php

namespace Admin\Base\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Setup the admin settings
 **/
class ConfigureSettingsPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $services = [];
        foreach ($container->findTaggedServiceIds('admin_base.settings_panel') as $service => $tag) {
            if (!isset($tag[0]['alias'])) {
                throw new \InvalidArgumentException(sprintf('The service %s tagged with "admin_base.settings_panel" must set the "alias" option in the tag.', $service));
            }
            $name = $tag[0]['alias'];
            $services[$name] = new Reference($service);

            $container->getDefinition($service)->setPublic(false);
        }

        $definition = $container->getDefinition('admin_base.settings_panel_registry');
        $definition->replaceArgument(1, $services);
    }
}
