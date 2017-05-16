<?php

namespace Perform\DevBundle\DependencyInjection;

use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Perform\DevBundle\BundleResource\ContactBundleResource;
use Perform\DevBundle\BundleResource\MediaBundleResource;

/**
 * PerformDevExtension.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class PerformDevExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');

        $registry = $container->getDefinition('perform_dev.resource_registry');
        $registry->addMethodCall('addResource', [new Definition(ContactBundleResource::class)]);
        $registry->addMethodCall('addResource', [new Definition(MediaBundleResource::class)]);
    }
}
