<?php

namespace Perform\CmsBundle\DependencyInjection;

use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;

/**
 * Configuration
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('perform_cms');

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
            ->end()
            ;

        return $treeBuilder;
    }
}
