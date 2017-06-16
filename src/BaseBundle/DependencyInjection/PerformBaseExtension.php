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
use Perform\BaseBundle\Doctrine\EntityResolver;

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

        $container->setParameter('perform_base.panels.left', $config['panels']['left']);
        $container->setParameter('perform_base.panels.right', $config['panels']['right']);
        $container->setParameter('perform_base.menu_order', $config['menu']['order']);
        $container->setParameter('perform_base.auto_asset_version', uniqid());
        $this->configureTypeRegistry($container);
        $this->configureMailer($config, $container);
        $this->findExtendedEntities($container, $config);
        $this->processAdminConfig($container, $config);
        $this->createSimpleMenus($container, $config['menu']['simple']);

        $tokenManager = $container->getDefinition('perform_base.reset_token_manager');
        $tokenManager->addArgument($config['security']['reset_token_expiry']);
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
        $definition->addMethodCall('addType', ['hidden', 'Perform\BaseBundle\Type\HiddenType']);
        $definition->addMethodCall('addType', ['duration', 'Perform\BaseBundle\Type\DurationType']);
        $definition->addMethodCall('addType', ['email', 'Perform\BaseBundle\Type\EmailType']);
        $definition->addMethodCall('addType', ['choice', 'Perform\BaseBundle\Type\ChoiceType']);
        $definition->addMethodCall('addTypeService', ['entity', 'perform_base.type.entity']);
        $definition->addMethodCall('addTypeService', ['collection', 'perform_base.type.collection']);
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

    protected function findExtendedEntities(ContainerBuilder $container, array $config)
    {
        $entityAliases = [];
        $extendedAliases = [];
        $entities = $this->findEntities($container);
        foreach ($entities as $class => $item) {
            $alias = $item[0];
            $entityAliases[$alias] = $class;
            if (false === $parent = $item[1]) {
                continue;
            }
            $parentAlias = $entities[$parent][0];
            $extendedAliases[$parentAlias] = $alias;
        }

        $container->setParameter('perform_base.entity_aliases', $entityAliases);
        $container->setParameter('perform_base.extended_entity_aliases', $extendedAliases);

        $extended = [];
        $resolver = new EntityResolver($entityAliases);
        foreach ($config['extended_entities'] as $parent => $child) {
            $extended[$resolver->resolveNoExtend($parent)] = $resolver->resolveNoExtend($child);
        }

        $container->setParameter('perform_base.extended_entities', $extended);
    }

    protected function findEntities(ContainerBuilder $container)
    {
        // can't use BundleSearcher here because kernel service isn't available
        $bundles = $container->getParameter('kernel.bundles');
        $entities = [];

        foreach ($bundles as $bundleClass) {
            $reflection = new \ReflectionClass($bundleClass);
            $bundleName = basename($reflection->getFileName(), '.php');
            $dir = dirname($reflection->getFileName()).'/Entity';
            if (!is_dir($dir)) {
                continue;
            }

            $finder = Finder::create()->files()->name('*.php')->in($dir);
            foreach ($finder as $file) {
                $entityClass = str_replace('/', '\\', $reflection->getNamespaceName().'\\Entity\\'.basename($file->getBasename('.php')));
                $refl = new \ReflectionClass($entityClass);
                $parent = $refl->getParentClass() ? $refl->getParentClass()->getName() : false;

                $filename = $refl->getFileName();
                $container->addResource(new FileResource($filename));
                $container->addResource(new DirectoryResource($dir));

                $entities[$entityClass] = [
                    $bundleName.':'.$file->getBasename('.php'),
                    $parent,
                ];
            }
        }

        return $entities;
    }

    protected function createSimpleMenus(ContainerBuilder $container, array $config)
    {
        foreach ($config as $alias => $options) {
            $definition = $container->register('perform_base.menu.simple.'.$alias, 'Perform\BaseBundle\Menu\SimpleLinkProvider');
            $definition->setArguments([$alias, $options['entity'], $options['route'], $options['icon']]);
            $definition->addTag('perform_base.link_provider', ['alias' => $alias]);
        }
    }

    public function processAdminConfig(ContainerBuilder $container, array $config)
    {
        $admins = [];
        $resolver = new EntityResolver($container->getParameter('perform_base.entity_aliases'));
        foreach ($config['admins'] as $entity => $configuration) {
            $admins[$resolver->resolveNoExtend($entity)] = $configuration;
        }

        $container->setParameter('perform_base.admins', $admins);
    }
}
