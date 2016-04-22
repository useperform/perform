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
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');

        $this->configureTypeRegistry($container);
        $this->configureMailer($config, $container);
    }

    protected function configureTypeRegistry(ContainerBuilder $container)
    {
        $definition = $container->register('admin_base.type_registry', 'Admin\Base\Type\TypeRegistry');
        $definition->addArgument(new Reference('service_container'));
        $definition->addMethodCall('addType', ['string', 'Admin\Base\Type\StringType']);
        $definition->addMethodCall('addType', ['text', 'Admin\Base\Type\TextType']);
        $definition->addMethodCall('addType', ['date', 'Admin\Base\Type\DateType']);
        $definition->addMethodCall('addType', ['datetime', 'Admin\Base\Type\DateTimeType']);
    }

    protected function configureMailer(array $config, ContainerBuilder $container)
    {
        if (!$container->hasParameter('noreply@glynnforrest.com')) {
            $container->setParameter('admin_base.mailer.from_address', 'noreply@glynnforrest.com');
        }

        if (!isset($config['mailer']['excluded_domains'])) {
            return;
        }

        $definition = $container->getDefinition('admin_base.email.mailer');
        $definition->addMethodCall('setExcludedDomains', [$config['mailer']['excluded_domains']]);
    }
}
