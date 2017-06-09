<?php

namespace Perform\DevBundle\DependencyInjection;

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
        $rootNode = $treeBuilder->root('perform_dev');

        $rootNode
            ->children()
                ->arrayNode('skeleton_vars')
                    ->children()
                        ->scalarNode('app_name')
                            ->info('Application name to use in titles and headings')
                        ->end()
                        ->scalarNode('app_name_lowercase')
                            ->info('Application name to use in configuration files, e.g. composer.json, package.json')
                        ->end()
                        ->scalarNode('dev_url')
                            ->info('The url of the app for local development, e.g. http://myapp.dev')
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
