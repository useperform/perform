<?php

namespace Admin\Base\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Setup the admin menu
 **/
class ConfigureMenuPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $container->setParameter('knp_menu.renderer.twig.template', 'AdminBaseBundle::menu.html.twig');

        $services = [];
        foreach ($container->findTaggedServiceIds('admin_base.link_provider') as $service => $tag) {
            if (!isset($tag[0]['alias'])) {
                throw new \InvalidArgumentException(sprintf('The service %s tagged with "admin_base.link_provider" must set the "alias" option in the tag.', $service));
            }
            $alias = $tag[0]['alias'];
            $services[] = $service;
        }
        //sort services by admin_base.menu.order in the config file, if defined

        $definition = $container->getDefinition('admin_base.menu_builder');
        foreach ($services as $service) {
            $definition->addMethodCall('addLinkProvider', [new Reference($service)]);
        }
    }
}
