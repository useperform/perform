<?php

namespace Admin\Base\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Setup the admin menu
 **/
class ConfigureMenuPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $container->setParameter('knp_menu.renderer.twig.template', 'AdminBaseBundle::menu.html.twig');
    }
}
