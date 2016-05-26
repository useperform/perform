<?php

namespace Admin\Base\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\Config\Resource\DirectoryResource;
use Doctrine\Common\Persistence\Mapping\MappingException;

/**
 * AdminBaseExtension.
 **/
class AdminBaseExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $this->ensureUTC();
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');

        $container->setParameter('admin_base.admins', $config['admins']);
        $container->setParameter('admin_base.panels.left', $config['panels']['left']);
        $container->setParameter('admin_base.panels.right', $config['panels']['right']);
        $this->configureTypeRegistry($container);
        $this->configureMailer($config, $container);
        $this->findExtendedEntities($container);
    }

    protected function configureTypeRegistry(ContainerBuilder $container)
    {
        $definition = $container->register('admin_base.type_registry', 'Admin\Base\Type\TypeRegistry');
        $definition->addArgument(new Reference('service_container'));
        $definition->addMethodCall('addType', ['string', 'Admin\Base\Type\StringType']);
        $definition->addMethodCall('addType', ['text', 'Admin\Base\Type\TextType']);
        $definition->addMethodCall('addType', ['date', 'Admin\Base\Type\DateType']);
        $definition->addMethodCall('addType', ['datetime', 'Admin\Base\Type\DateTimeType']);
        $definition->addMethodCall('addType', ['boolean', 'Admin\Base\Type\BooleanType']);

        // pull from other bundles in a compiler pass
        $definition->addMethodCall('addTypeService', ['image', 'admin_media.type.image']);
    }

    protected function configureMailer(array $config, ContainerBuilder $container)
    {
        if (!$container->hasParameter('admin_base.mailer.from_address')) {
            $container->setParameter('admin_base.mailer.from_address', 'noreply@glynnforrest.com');
        }

        if (!isset($config['mailer']['excluded_domains'])) {
            return;
        }

        $definition = $container->getDefinition('admin_base.email.mailer');
        $definition->addMethodCall('setExcludedDomains', [$config['mailer']['excluded_domains']]);
    }

    /**
     * Stop the show if the server is running anything but UTC timezone.
     */
    protected function ensureUTC()
    {
        if ('UTC' !== date_default_timezone_get()) {
            throw new \Exception('The server timezone must be set to UTC');
        }
    }

    protected function findExtendedEntities(ContainerBuilder $container)
    {
        $bundles = $container->getParameter('kernel.bundles');
        $entities = [];
        $entityAliases = [];
        foreach ($bundles as $bundleName => $bundleClass) {
            $reflection = new \ReflectionClass($bundleClass);
            $dirname = dirname($reflection->getFileName());
            $namespace = $reflection->getNamespaceName().'\\Entity\\';

            if (is_dir($dir = $dirname.'/Entity')) {
                foreach (Finder::create()->files()->in($dir)->name('*.php') as $file) {
                    $entityClass = $namespace.$file->getBasename('.php');
                    if (!class_exists($entityClass)) {
                        continue;
                    }

                    $entityReflection = new \ReflectionClass($entityClass);

                    $entities[$entityClass] = $entityReflection->getParentClass() ? $entityReflection->getParentClass()->getName() : false;
                    $aliases[$entityClass] = $bundleName.':'.$file->getBasename('.php');
                    $container->addResource(new FileResource($file->getRealpath()));
                }
                $container->addResource(new DirectoryResource($dir));
            }
        }

        $extendedEntities = [];
        $extendedAliases = [];
        foreach ($entities as $child => $parent) {
            if (isset($entities[$parent])) {
                //skip if parent is abstract
                if (isset($extendedEntities[$parent])) {
                    throw new MappingException(sprintf('Unable to auto-extend parent entity "%s" in child entity "%s", as it has already been extended by "%s".', $parent, $child, $extendedEntities[$parent]));
                }

                $extendedEntities[$parent] = $child;
                $extendedAliases[$aliases[$parent]] = $aliases[$child];
            }
        }

        $container->setParameter('admin_base.extended_entities', $extendedEntities);
        $container->setParameter('admin_base.entity_aliases', array_flip($aliases));
        $container->setParameter('admin_base.extended_entity_aliases', $extendedAliases);
    }
}
