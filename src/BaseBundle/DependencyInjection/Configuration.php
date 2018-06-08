<?php

namespace Perform\BaseBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('perform_base');

        $rootNode
            ->fixXmlConfig('extended_entity', 'extended_entities')
            ->children()
                ->arrayNode('assets')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('entrypoints')
                            ->useAttributeAsKey('name')
                            ->prototype('variable')
                                ->validate()
                                    ->ifTrue(function ($value) {
                                        if (is_string($value)) {
                                            return false;
                                        }
                                        if (!is_array($value) || empty($value)) {
                                            return true;
                                        }
                                        foreach ($value as $path) {
                                            if (!is_string($path)) {
                                                return true;
                                            }
                                        }

                                        return false;
                                    })
                                    ->thenInvalid('Each asset entrypoint must be a filename or an array of filenames.')
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('namespaces')
                            ->useAttributeAsKey('name')
                            ->prototype('scalar')->end()
                        ->end()
                        ->arrayNode('extra_js')
                            ->prototype('scalar')->end()
                        ->end()
                        ->arrayNode('extra_sass')
                            ->prototype('scalar')->end()
                        ->end()
                        ->scalarNode('theme')
                            ->defaultValue('~perform-base/scss/themes/default')
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('doctrine')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('resolve')
                            ->useAttributeAsKey('interface')
                            ->prototype('variable')
                                ->validate()
                                    ->ifTrue(function ($value) {
                                        if (is_string($value)) {
                                            return false;
                                        }
                                        if (!is_array($value) || empty($value)) {
                                            return true;
                                        }
                                        foreach (array_merge(array_keys($value), array_values($value)) as $item) {
                                            if (!is_string($item)) {
                                                return true;
                                            }
                                        }

                                        return false;
                                    })
                                    ->thenInvalid('Each resolved entity value must be either a class name, or an array of class names with the related entities as keys.')
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('extended_entities')
                    ->useAttributeAsKey('parent')
                    ->prototype('scalar')
                        ->cannotBeEmpty()
                    ->end()
                ->end()
                ->arrayNode('menu')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('simple')
                            ->useAttributeAsKey('alias')
                            ->prototype('array')
                                ->children()
                                    ->scalarNode('route')
                                        ->defaultNull()
                                    ->end()
                                    ->scalarNode('crud')
                                        ->defaultNull()
                                    ->end()
                                    ->scalarNode('icon')
                                        ->defaultNull()
                                    ->end()
                                    ->integerNode('priority')
                                        ->defaultValue(0)
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('order')
                            ->prototype('scalar')->end()
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
