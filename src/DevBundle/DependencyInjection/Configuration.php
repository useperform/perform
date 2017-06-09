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
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
