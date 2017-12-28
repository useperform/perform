<?php

namespace Perform\BaseBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\Finder\Finder;
use Perform\BaseBundle\Doctrine\EntityResolver;
use Perform\Licensing\Licensing;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class PerformBaseExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        Licensing::validateProject($container);
        // $this->ensureUTC();
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');

        $container->setParameter('perform_base.panels.left', $config['panels']['left']);
        $container->setParameter('perform_base.panels.right', $config['panels']['right']);
        $container->setParameter('perform_base.menu_order', $config['menu']['order']);
        $container->setParameter('perform_base.auto_asset_version', uniqid());
        $container->setParameter('perform_base.theme', $config['assets']['theme']);
        self::addExtraSass($container, $config['assets']['extra_sass']);
        $this->configureTypeRegistry($container);
        $this->configureMailer($config, $container);
        $this->findExtendedEntities($container, $config);
        $this->processAdminConfig($container, $config);
        $this->createSimpleMenus($container, $config['menu']['simple']);
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
        $definition->addMethodCall('addTypeService', ['slug', 'perform_base.type.slug']);
    }

    protected function configureMailer(array $config, ContainerBuilder $container)
    {
        if (!isset($config['mailer']['from_address'])) {
            $container->removeDefinition('perform_base.email.mailer');

            return;
        }

        $container->setParameter('perform_base.mailer.from_address', $config['mailer']['from_address']);

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
        if ('UTC' !== $timezone = date_default_timezone_get()) {
            throw new \Exception(sprintf('The server timezone must be set to UTC, it is currently "%s".', $timezone));
        }
    }

    protected function findExtendedEntities(ContainerBuilder $container, array $config)
    {
        $aliases = $this->findEntityAliases($container);
        $container->setParameter('perform_base.entity_aliases', $aliases);

        $extended = [];
        $resolver = new EntityResolver($aliases);
        $baseError = ' Make sure the configuration of perform_base:extended_entities contains valid entity classnames or aliases, e.g. SomeBundle\Entity\Item or SomeBundle:Item.';

        foreach ($config['extended_entities'] as $parent => $child) {
            $parentClass = $resolver->resolveNoExtend($parent);
            if (!class_exists($parentClass)) {
                throw new \InvalidArgumentException(sprintf('Parent entity class "%s" does not exist.', $parentClass).$baseError);
            }

            $childClass = $resolver->resolveNoExtend($child);
            if (!class_exists($childClass)) {
                throw new \InvalidArgumentException(sprintf('Child entity class "%s" does not exist.', $childClass).$baseError);
            }

            $extended[$parentClass] = $resolver->resolveNoExtend($child);
        }

        $container->setParameter('perform_base.extended_entities', $extended);
    }

    protected function findEntityAliases(ContainerBuilder $container)
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
                $classname = str_replace('/', '\\', $reflection->getNamespaceName().'\\Entity\\'.basename($file->getBasename('.php')));
                $alias = $bundleName.':'.$file->getBasename('.php');
                $aliases[$alias] = $classname;
            }
        }

        return $aliases;
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

    /**
     * Add extra files to be included in the sass build.
     *
     * This method is available to other bundles so they don't have to
     * implement PrependExtensionInterface to add extra sass files.
     *
     * @param ContainerBuilder $container
     * @param array            $files
     */
    public static function addExtraSass(ContainerBuilder $container, array $files)
    {
        $param = 'perform_base.extra_sass';
        $existing = $container->hasParameter($param) ? $container->getParameter($param) : [];
        $container->setParameter($param, array_merge($existing, $files));
    }
}
