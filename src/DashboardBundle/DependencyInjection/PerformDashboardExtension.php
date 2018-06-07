<?php

namespace Perform\DashboardBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;
use Perform\Licensing\Licensing;
use Perform\BaseBundle\DependencyInjection\Assets;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class PerformDashboardExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        Licensing::validateProject($container);
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $container->setParameter('perform_dashboard.panels.left', $config['panels']['left']);
        $container->setParameter('perform_dashboard.panels.right', $config['panels']['right']);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');

        Assets::addNamespace($container, 'perform-dashboard', __DIR__.'/../Resources');
        Assets::addExtraSass($container, '~perform-dashboard/scss/dashboard.scss');
    }
}
