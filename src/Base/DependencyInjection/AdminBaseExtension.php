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
        $this->ensureUTC();
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');

        $container->setParameter('admin_base.admins', $config['admins']);
        $container->setParameter('admin_base.panels.left', $config['panels']['left']);
        $container->setParameter('admin_base.panels.right', $config['panels']['right']);
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
        $definition->addMethodCall('addType', ['boolean', 'Admin\Base\Type\BooleanType']);
    }

    protected function configureMailer(array $config, ContainerBuilder $container)
    {
        if (!$container->hasParameter('admin_base.mailer.from_address')) {
            $container->setParameter('admin_base.mailer.from_address', 'noreply@glynnforrest.com');
        }

        if (!isset($config['mailer']['excluded_domains'])) {
            return;
        }

        $definition = $container->getDefinition('admin_base.email.mailer');
        $definition->addMethodCall('setExcludedDomains', [$config['mailer']['excluded_domains']]);
    }

    /**
     * Stop the show if the server is running anything but UTC timezone.
     */
    protected function ensureUTC()
    {
        if ('UTC' !== date_default_timezone_get()) {
            throw new \Exception('The server timezone must be set to UTC');
        }
    }
}
