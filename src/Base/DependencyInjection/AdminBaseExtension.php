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
use Admin\Base\Util\BundleSearcher;

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
        $container->setParameter('admin_base.menu_order', isset($config['menu']['order']) ? $config['menu']['order'] : []);
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
        $definition->addMethodCall('addType', ['password', 'Admin\Base\Type\PasswordType']);
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
        $mapper = function($class, $classBasename, $bundleName, $bundleClass) use ($container) {
            $refl = new \ReflectionClass($class);
            $parent = $refl->getParentClass() ? $refl->getParentClass()->getName() : false;
            //skip if parent is abstract

            $file = $refl->getFileName();
            $container->addResource(new FileResource($file));
            $container->addResource(new DirectoryResource(dirname($file)));

            return [
                $bundleName.':'.$classBasename,
                $parent
            ];
        };
        $searcher = new BundleSearcher($container);
        $entities = $searcher->findItemsInNamespaceSegment('Entity', $mapper);

        $extendedEntities = [];
        $entityAliases = [];
        $extendedAliases = [];
        foreach ($entities as $class => $item) {
            $alias = $item[0];
            $entityAliases[$alias] = $class;
            if (false === $parent = $item[1]) {
                continue;
            }
            if (isset($extendedEntities[$parent])) {
                throw new MappingException(sprintf('Unable to auto-extend parent entity "%s" in child entity "%s", as it has already been extended by "%s".', $parent, $child, $extendedEntities[$parent]));
            }

            $extendedEntities[$parent] = $class;
            $parentAlias = $entities[$parent][0];
            $extendedAliases[$parentAlias] = $alias;
        }

        $container->setParameter('admin_base.extended_entities', $extendedEntities);
        $container->setParameter('admin_base.entity_aliases', $entityAliases);
        $container->setParameter('admin_base.extended_entity_aliases', $extendedAliases);
    }
}
