<?php

namespace Perform\DevBundle\DependencyInjection;

use Perform\DevBundle\Npm\DependenciesInterface;
use Perform\Licensing\Licensing;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class PerformDevExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        Licensing::validateProject($container);
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');

        $devConfigFile = $container->getParameter('kernel.root_dir').'/config/config_dev.yml';
        $container->getDefinition('perform_dev.twig.config')
            ->setArguments([
                new Definition(Configuration::class),
                $config,
                $devConfigFile,
            ]);

        $container->registerForAutoconfiguration(DependenciesInterface::class)
            ->addTag('perform_dev.npm_dependencies');
    }
}
