<?php

namespace Perform\BaseBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\Finder\Finder;
use Perform\BaseBundle\Doctrine\EntityResolver;
use Perform\Licensing\Licensing;
use Perform\BaseBundle\FieldType\FieldTypeInterface;
use Perform\BaseBundle\FieldType\FieldTypeRegistry;
use Money\Money;
use Perform\BaseBundle\EventListener\SimpleMenuListener;
use Perform\BaseBundle\Event\MenuEvent;
use Perform\BaseBundle\Crud\CrudInterface;
use Symfony\Component\DependencyInjection\Reference;

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
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');

        if (class_exists(Money::class)) {
            $loader->load('services/money.yml');
        }

        $container->setParameter('perform_base.menu_order', $config['menu']['order']);
        $container->setParameter('perform_base.auto_asset_version', uniqid());
        $container->setParameter('perform_base.assets.theme', $config['assets']['theme']);
        $this->configureCrud($container);
        $this->findExtendedEntities($container, $config);
        $this->configureResolvedEntities($container, $config);
        $this->createSimpleMenus($container, $config['menu']['simple']);
        $this->configureAssets($container, $config['assets']);
    }

    protected function configureCrud(ContainerBuilder $container)
    {
        $container->registerForAutoconfiguration(FieldTypeInterface::class)
            ->addTag('perform_base.field_type');

        $container->getDefinition('perform_base.listener.crud_template')
            ->setArgument(0, LoopableServiceLocator::createDefinition([
                'registry' => new Reference('perform_base.crud.registry'),
                'twig' => new Reference('twig'),
            ]));
    }

    protected function configureResolvedEntities(ContainerBuilder $container, array $config)
    {
        $baseError = ' Make sure the configuration of perform_base:doctrine:resolve contains valid class and interface names.';
        foreach ($config['doctrine']['resolve'] as $interface => $value) {
            if (!interface_exists($interface)) {
                throw new \InvalidArgumentException(sprintf('Entity interface "%s" does not exist.', $interface).$baseError);
            }
            $classes = is_string($value) ? [$value] : array_merge(array_keys($value), array_values($value));
            foreach ($classes as $class) {
                if (!class_exists($class)) {
                    throw new \InvalidArgumentException(sprintf('Entity class "%s" does not exist.', $class).$baseError);
                }
            }
        }

        $container->setParameter(Doctrine::PARAM_RESOLVED_CONFIG, $config['doctrine']['resolve']);
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
        $aliases = [];

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
        foreach ($config as $name => $options) {
            $definition = $container->register('perform_base.menu.simple.'.$name, SimpleMenuListener::class);
            $definition->setArguments([$name, $options['crud'], $options['route'], $options['icon'], $options['priority']]);
            $definition->addTag('kernel.event_listener', ['event' => MenuEvent::BUILD, 'method' => 'onMenuBuild']);
        }
    }

    protected function configureAssets(ContainerBuilder $container, array $config)
    {
        Assets::addNpmConfig($container, __DIR__.'/../package.json');
        Assets::addEntryPoint($container, 'perform', [__DIR__.'/../Resources/src/perform.js', __DIR__.'/../Resources/scss/perform.scss']);
        Assets::addNamespace($container, 'perform-base', __DIR__.'/../Resources');
        Assets::addExtraJavascript($container, 'base', 'perform-base/src/module');

        foreach ($config['entrypoints'] as $name => $path) {
            Assets::addEntryPoint($container, $name, $path);
        }
        foreach ($config['namespaces'] as $name => $path) {
            Assets::addNamespace($container, $name, $path);
        }
        foreach ($config['extra_js'] as $name => $path) {
            Assets::addExtraJavascript($container, $name, $path);
        }
        foreach ($config['extra_sass'] as $path) {
            Assets::addExtraSass($container, $path);
        }
        // if no sass has been added, ensure that the extra_sass parameter will still be created
        if (!$container->hasParameter(Assets::PARAM_EXTRA_SASS)) {
            $container->setParameter(Assets::PARAM_EXTRA_SASS, []);
        }
    }
}
