<?php

namespace Perform\BaseBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\Config\Resource\DirectoryResource;
use Doctrine\Common\Persistence\Mapping\MappingException;
use Perform\BaseBundle\Util\BundleSearcher;

/**
 * PerformBaseExtension.
 **/
class PerformBaseExtension extends Extension
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

        $container->setParameter('perform_base.admins', $config['admins']);
        $container->setParameter('perform_base.panels.left', $config['panels']['left']);
        $container->setParameter('perform_base.panels.right', $config['panels']['right']);
        $container->setParameter('perform_base.menu_order', isset($config['menu']['order']) ? $config['menu']['order'] : []);
        $container->setParameter('perform_base.auto_asset_version', uniqid());
        $this->configureTypeRegistry($container);
        $this->configureMailer($config, $container);
        $this->findExtendedEntities($container);
    }

    protected function configureTypeRegistry(ContainerBuilder $container)
    {
        $definition = $container->register('perform_base.type_registry', 'Perform\BaseBundle\Type\TypeRegistry');
        $definition->addArgument(new Reference('service_container'));
        $definition->addMethodCall('addType', ['string', 'Perform\BaseBundle\Type\StringType']);
        $definition->addMethodCall('addType', ['text', 'Perform\BaseBundle\Type\TextType']);
        $definition->addMethodCall('addType', ['password', 'Perform\BaseBundle\Type\PasswordType']);
        $definition->addMethodCall('addType', ['date', 'Perform\BaseBundle\Type\DateType']);
        $definition->addMethodCall('addType', ['datetime', 'Perform\BaseBundle\Type\DateTimeType']);
        $definition->addMethodCall('addType', ['boolean', 'Perform\BaseBundle\Type\BooleanType']);
        $definition->addMethodCall('addType', ['integer', 'Perform\BaseBundle\Type\IntegerType']);

        // pull from other bundles in a compiler pass
        $definition->addMethodCall('addTypeService', ['media', 'perform_media.type.media']);
    }

    protected function configureMailer(array $config, ContainerBuilder $container)
    {
        if (!$container->hasParameter('perform_base.mailer.from_address')) {
            $container->setParameter('perform_base.mailer.from_address', 'noreply@glynnforrest.com');
        }

        if (!isset($config['mailer']['excluded_domains'])) {
            return;
        }

        $definition = $container->getDefinition('perform_base.email.mailer');
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

        $container->setParameter('perform_base.extended_entities', $extendedEntities);
        $container->setParameter('perform_base.entity_aliases', $entityAliases);
        $container->setParameter('perform_base.extended_entity_aliases', $extendedAliases);
    }
}
