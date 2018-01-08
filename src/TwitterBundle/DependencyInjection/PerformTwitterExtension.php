<?php

namespace Perform\TwitterBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\Config\Definition\Exception\UnsetKeyException;
use Perform\Licensing\Licensing;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class PerformTwitterExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        Licensing::validateProject($container);
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);
        foreach (['screen_name', 'cache_ttl'] as $key) {
            $container->setParameter('perform_twitter.'.$key, $config[$key]);
        }

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');

        $this->configureClient($config, $container);
    }

    protected function configureClient(array $config, ContainerBuilder $container)
    {
        $credentialsSource = $config['credentials_source'];
        if ($credentialsSource === 'config') {
            $definition = $container->register('perform_twitter.factory', 'Perform\TwitterBundle\Factory\InMemoryFactory');
            $keys = [
                'consumer_key',
                'consumer_secret',
                'access_token',
                'access_token_secret',
            ];
            foreach ($keys as $key) {
                if (!isset($config['credentials'][$key])) {
                    throw new UnsetKeyException('perform_twitter.credentials.'.$key.' must be set.');
                }

                $definition->addArgument($config['credentials'][$key]);
            }
        } else {
            $definition = $container->register('perform_twitter.factory', 'Perform\TwitterBundle\Factory\SettingsFactory');
            // $defintion->addArgument(new Reference('perform_base.settings.manager'));
        }

        $client = $container->getDefinition('perform_twitter.client');
        $client->addArgument(new Reference('perform_twitter.factory'));
        $client->addArgument(new Reference('doctrine_cache.providers.'.$config['cache_provider']));
        $client->addArgument($container->getParameter('perform_twitter.cache_ttl'));
        $client->addMethodCall('setLogger', [new Reference('logger')]);
    }
}
