<?php

namespace Perform\PageEditorBundle\DependencyInjection;

use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('perform_page_editor');

        $rootNode
            ->children()
                ->arrayNode('toolbar')
                    ->children()
                        ->arrayNode('excluded_urls')
                            ->prototype('scalar')->end()
                            ->isRequired()
                            ->defaultValue([
                                '^/(_(profiler|wdt)|css|images|js)/',
                                '^/admin',
                            ])
                        ->end()
                    ->end()
                    ->addDefaultsIfNotSet()
                ->end()
                ->arrayNode('fixtures')
                    ->useAttributeAsKey('page')
                    ->prototype('array')
                        ->children()
                            ->arrayNode('sections')
                                ->useAttributeAsKey('name')
                                ->prototype('array')
                                    ->children()
                                        ->scalarNode('profile')->isRequired()->end()
                                    ->end()
                                ->end()
                            ->end()
                            ->scalarNode('versions')
                                ->defaultValue(4)
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
            ;

        return $treeBuilder;
    }
}
