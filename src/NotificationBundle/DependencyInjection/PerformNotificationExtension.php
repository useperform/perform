<?php

namespace Perform\NotificationBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\DependencyInjection\Reference;
use Psr\Log\LogLevel;
use Perform\Licensing\Licensing;

class PerformNotificationExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        Licensing::validateProject($container);
        $configuration = $this->getConfiguration($configs, $container);
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');

        if ($config['logging']['enabled']) {
            $notifier = $container->getDefinition('perform_notification.notifier');
            $notifier->addTag('monolog.logger', ['channel' => 'notification']);

            $level = isset($config['logging']['level']) ? $config['logging']['level'] : LogLevel::INFO;
            $notifier->addMethodCall('setLogger', [new Reference('logger'), $level]);
        }

        $container->setParameter(
            'perform_notification.email_default_from',
            isset($config['email']['default_from']) ? $config['email']['default_from'] : []
        );

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
