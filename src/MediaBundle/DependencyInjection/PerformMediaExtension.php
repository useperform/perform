<?php

namespace Perform\MediaBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;
use Perform\BaseBundle\DependencyInjection\PerformBaseExtension;
use Perform\Licensing\Licensing;
use Symfony\Component\DependencyInjection\ServiceLocator;
use Symfony\Component\DependencyInjection\Reference;
use Perform\MediaBundle\Bucket\Bucket;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class PerformMediaExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        Licensing::validateProject($container);
        $config = $this->processConfiguration(new Configuration(), $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');

        $bucketServices = [];
        foreach ($config['buckets'] as $name => $bucketConfig) {
            $bucketServices[$name] = new Reference($this->registerBucket($name, $bucketConfig, $container));
        }
        $defaultBucket = empty($config['buckets']) ? '' : array_keys($config['buckets'])[0];

        $container->getDefinition('perform_media.bucket_registry')
            ->setArgument(1, $defaultBucket);

        $bucketLocator = $container->register('perform_media.bucket_locator', ServiceLocator::class)
                       ->setPublic(false)
                       ->addTag('container.service_locator')
                       ->setArguments([$bucketServices]);

        if (class_exists(PerformBaseExtension::class)) {
            PerformBaseExtension::addExtraSass($container, ['PerformMediaBundle:media.scss']);
        }
    }

    protected function registerBucket($name, array $config, ContainerBuilder $container)
    {
        $service = sprintf('perform_media.bucket.%s', $name);
        $definition = $container->register($service, Bucket::class)
                    ->setPublic(false)
                    ->setArguments([
                        $name,
                        new Reference($config['flysystem']),
                        new Reference($config['url_generator']),
                        ['image', 'other'],
                    ]);

        return $service;
    }
}
