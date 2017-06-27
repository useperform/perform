<?php

namespace Perform\NotificationBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\DependencyInjection\Reference;
use Psr\Log\LogLevel;

class PerformNotificationExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = $this->getConfiguration($configs, $container);
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');

        $notifier = $container->getDefinition('perform_notification.notifier');
        $notifier->addMethodCall('setDefaultPublishers', [$config['default_publishers']]);

        if ($config['logging']['enabled']) {
            $level = isset($config['logging']['level']) ? $config['logging']['level'] : LogLevel::INFO;
            $notifier->addTag('monolog.logger', ['channel' => 'notification']);
            $notifier->addMethodCall('setLogger', [new Reference('logger'), $level]);
        }

        if (isset($config['active_recipient_provider'])) {
            $twigExtension = $container->getDefinition('perform_notification.twig.notification');
            $twigExtension->replaceArgument(0, new Reference($config['active_recipient_provider']));
        }
    }

    public function getConfiguration(array $config, ContainerBuilder $container)
    {
        return new Configuration($container->getParameter('kernel.debug'));
    }
}
