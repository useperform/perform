<?php

namespace Perform\AnalyticsBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * PerformAnalyticsExtension.
 **/
class PerformAnalyticsExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');

        $twigExtension = $container->getDefinition('perform_analytics.twig.analytics');
        $twigExtension->addArgument($config['vendors']);

        $settingsPanel = $container->getDefinition('perform_analytics.settings.analytics');
        $settingsPanel->addArgument($config['vendors']);
    }
}
