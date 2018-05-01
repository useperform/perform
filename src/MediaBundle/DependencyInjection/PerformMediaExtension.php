<?php

namespace Perform\MediaBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;
use Perform\BaseBundle\DependencyInjection\PerformBaseExtension;
use Perform\Licensing\Licensing;
use Perform\BaseBundle\DependencyInjection\Assets;

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
        $container->setParameter('perform_media.bucket_configs', $config['buckets']);

        if (class_exists(Assets::class)) {
            Assets::addNamespace($container, 'perform-media', __DIR__.'/../Resources');
            Assets::addExtraSass($container, ['~perform-media/scss/media.scss']);
        }
    }
}
