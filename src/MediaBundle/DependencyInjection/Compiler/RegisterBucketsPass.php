<?php

namespace Perform\MediaBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\ServiceLocator;
use Perform\MediaBundle\Bucket\Bucket;
use Perform\MediaBundle\Exception\MediaTypeException;
use Perform\MediaBundle\MediaType\MediaTypeInterface;
use Perform\MediaBundle\DependencyInjection\MediaTypeDefinitionFactory;
use Symfony\Component\DependencyInjection\Definition;

/**
 * Register the media buckets from configuration.
 *
 * This needs to be in a compiler pass because of the 'service' media type option.
 * The service definition is fetched from the container to get the
 * media type name, and not all services may be loaded when the media
 * bundle extension is loaded.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class RegisterBucketsPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $bucketConfigs = $container->getParameter('perform_media.bucket_configs');
        $container->getParameterBag()->remove('perform_media.bucket_configs');

        $bucketServices = [];
        foreach ($bucketConfigs as $name => $bucketConfig) {
            $bucketServices[$name] = new Reference($this->registerBucket($name, $bucketConfig, $container));
        }
        $defaultBucket = empty($bucketConfigs) ? '' : array_keys($bucketConfigs)[0];

        $bucketLocator = (new Definition(ServiceLocator::class))
                       ->setPublic(false)
                       ->addTag('container.service_locator')
                       ->setArguments([$bucketServices]);

        $container->getDefinition('perform_media.bucket_registry')
            ->setArguments([
                $bucketLocator,
                $defaultBucket,
            ]);
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
                        $this->registerMediaTypes($name, $config, $container),
                    ]);

        return $service;
    }

    protected function registerMediaTypes($bucketName, array $config, ContainerBuilder $container)
    {
        $types = [];
        foreach ($config['types'] as $typeConfig) {
            list($typeName, $definition) = $this->registerMediaType($bucketName, $typeConfig, $container);
            if (isset($types[$typeName])) {
                throw new MediaTypeException(sprintf('The "%s" bucket already has a media type "%s" registered. A media type can only be added to a bucket once. Create different buckets for different type configurations.', $bucketName, $typeName));
            }
            $types[$typeName] = $definition;
        }

        return $types;
    }

    protected function registerMediaType($bucketName, $typeConfig, ContainerBuilder $container)
    {
        try {
            if (isset($typeConfig['service'])) {
                // the reason for this compiler pass
                $definition = $container->getDefinition($typeConfig['service']);
                $class = $definition->getClass();

                return [$class::getName(), new Reference($typeConfig['service'])];
            }

            $factory = new MediaTypeDefinitionFactory();
            $definition = $factory->create($typeConfig);
        } catch (\Exception $e) {
            $msg = sprintf('An error occurred creating a type for the "%s" bucket: %s To register a custom media type, create a service implementing %s and reference it in the "types" node of the bucket configuration: ', $bucketName, $e->getMessage(), MediaTypeInterface::class);
            $msg .= <<<EOF


perform_media:
    buckets:
        {$bucketName}:
            types:
                - {service: 'app.media_type.custom_type'}
EOF;
            throw new MediaTypeException($msg, 1, $e);
        }

        $definition->setPublic(false);
        $class = $definition->getClass();
        $typeName = $class::getName();
        $container->setDefinition(sprintf('perform_media.media_type.%s.%s', $bucketName, $typeName), $definition);

        return [$typeName, $definition];
    }
}
