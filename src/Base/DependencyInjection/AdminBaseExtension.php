<?php

namespace Admin\Base\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\DependencyInjection\Reference;

/**
 * AdminBaseExtension.
 **/
class AdminBaseExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');

        $definition = $container->register('admin_base.type_registry', 'Admin\Base\Type\TypeRegistry');
        $definition->addArgument(new Reference('service_container'));
        $definition->addMethodCall('addType', ['string', 'Admin\Base\Type\StringType']);
        $definition->addMethodCall('addType', ['text', 'Admin\Base\Type\TextType']);
        $definition->addMethodCall('addType', ['datetime', 'Admin\Base\Type\DateTimeType']);
    }
}
