<?php

namespace Perform\DevBundle\DependencyInjection;

use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Perform\DevBundle\BundleResource as R;

/**
 * PerformDevExtension.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class PerformDevExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
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

        $this->defineBundleResources($container);
    }

    protected function defineBundleResources(ContainerBuilder $container)
    {
        $registry = $container->getDefinition('perform_dev.resource_registry');
        $registry->addMethodCall('addParentResource', [new Definition(R\ContactBundleResource::class)]);
        $registry->addMethodCall('addParentResource', [new Definition(R\MediaBundleResource::class)]);
        $registry->addMethodCall('addParentResource', [new Definition(R\UserResource::class)]);
        $registry->addMethodCall('addResource', [new Definition(R\OneupFlysystemResource::class)]);
    }
}
