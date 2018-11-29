<?php

namespace Perform\NotificationBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\DependencyInjection\Reference;
use Psr\Log\LogLevel;
use Perform\Licensing\Licensing;
use Perform\NotificationBundle\Preference\SettingsPreference;
use Perform\NotificationBundle\Preference\StaticPreference;
use Perform\NotificationBundle\Publisher\PublisherInterface;
use Perform\NotificationBundle\Preference\PreferenceInterface;

class PerformNotificationExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        Licensing::validateProject($container);
        $configuration = $this->getConfiguration($configs, $container);
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');

        $this->configureLogging($container, $config);
        $this->configurePreferences($container, $config['preferences']);

        $container->setParameter(
            'perform_notification.email_default_from',
            isset($config['email']['default_from']) ? $config['email']['default_from'] : []
        );

        if (isset($config['active_recipient_provider'])) {
            $twigExtension = $container->getDefinition('perform_notification.twig.notification');
            $twigExtension->replaceArgument(0, new Reference($config['active_recipient_provider']));
        }

        $container->registerForAutoconfiguration(PublisherInterface::class)
            ->addTag('perform_notification.publisher');
    }

    public function getConfiguration(array $config, ContainerBuilder $container)
    {
        return new Configuration($container->getParameter('kernel.debug'));
    }

    private function configureLogging(ContainerBuilder $container, array $config)
    {
        $service = 'perform_notification.listener.log';
        if (!$config['logging']['enabled']) {
            $container->removeDefinition($service);

            return;
        }

        $level = isset($config['logging']['level']) ? $config['logging']['level'] : LogLevel::INFO;
        $container->getDefinition($service)
            ->setArgument(1, $level);
    }

    private function configurePreferences(ContainerBuilder $container, array $preferencesConfig)
    {
        if (empty($preferencesConfig)) {
            $serviceName = 'perform_notification.preference.default';
            $container->register($serviceName, StaticPreference::class)
                ->setArguments([true]);
            $container->setAlias(PreferenceInterface::class, $serviceName);
            return;
        }

        foreach ($preferencesConfig as $name => $config) {
            $serviceName = 'perform_notification.preference.'.$name;

            switch ($config['type']) {
            case 'service':
                $container->setAlias($serviceName, $config['service']);
                break;
            case 'static':
                $container->register($serviceName, StaticPreference::class)
                    ->setArguments([$config['default']]);
                break;
            case 'settings':
                $container->register($serviceName, SettingsPreference::class)
                    ->setArguments([
                        new Reference('perform_base.settings_manager'),
                        $config['prefix'],
                        $config['default'],
                    ]);
                break;
            }
        }

        $defaultService = 'perform_notification.preference.'.array_keys($preferencesConfig)[0];
        $container->setAlias(PreferenceInterface::class, $defaultService);
    }
}
