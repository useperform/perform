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

        $bucketLocator = $container->register('perform_media.bucket_locator', ServiceLocator::class)
                       ->setPublic(false)
                       ->addTag('container.service_locator')
                       ->setArguments([[
                           'main' => new Reference('perform_media.bucket.main'),
                       ]]);

        $container->setParameter('perform_media.plugins', $config['plugins']);
        if (class_exists(PerformBaseExtension::class)) {
            PerformBaseExtension::addExtraSass($container, ['PerformMediaBundle:media.scss']);
        }
    }
}
