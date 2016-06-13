<?php

namespace Admin\TwitterBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\Config\Definition\Exception\UnsetKeyException;

/**
 * AdminTwitterExtension.
 **/
class AdminTwitterExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);
        foreach (['screen_name', 'cache_ttl'] as $key) {
            $container->setParameter('admin_twitter.'.$key, $config[$key]);
        }

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');

        $this->configureClient($config, $container);
    }

    protected function configureClient(array $config, ContainerBuilder $container)
    {
        $credentialsSource = $config['credentials_source'];
        if ($credentialsSource === 'config') {
            $definition = $container->register('admin_twitter.factory', 'Admin\TwitterBundle\Factory\InMemoryFactory');
            $keys = [
                'consumer_key',
                'consumer_secret',
                'access_token',
                'access_token_secret',
            ];
            foreach ($keys as $key) {
                if (!isset($config['credentials'][$key])) {
                    throw new UnsetKeyException('admin_twitter.credentials.'.$key.' must be set.');
                }

                $definition->addArgument($config['credentials'][$key]);
            }
        } else {
            $definition = $container->register('admin_twitter.factory', 'Admin\TwitterBundle\Factory\SettingsFactory');
            // $defintion->addArgument(new Reference('admin_base.settings.manager'));
        }

        $client = $container->getDefinition('admin_twitter.client');
        $client->addArgument(new Reference('admin_twitter.factory'));
        $client->addArgument(new Reference('doctrine_cache.providers.'.$config['cache_provider']));
        $client->addArgument($container->getParameter('admin_twitter.cache_ttl'));
        $client->addMethodCall('setLogger', [new Reference('logger')]);
    }
}
