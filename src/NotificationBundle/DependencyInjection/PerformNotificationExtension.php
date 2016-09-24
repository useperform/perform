<?php

namespace Perform\NotificationBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

class PerformNotificationExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');

        $notifier = $container->getDefinition('perform_notification.notifier');
        $notifier->addMethodCall('setDefaultPublishers', [$config['default_publishers']]);

        if (isset($config['active_recipient_provider'])) {
            $twigExtension = $container->getDefinition('perform_notification.twig.notification');
            $twigExtension->replaceArgument(0, new Reference($config['active_recipient_provider']));
        }

    }
}
