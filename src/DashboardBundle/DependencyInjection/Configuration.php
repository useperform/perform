<?php

namespace Perform\DashboardBundle\DependencyInjection;

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
        $rootNode = $treeBuilder->root('perform_dashboard');

        $rootNode
            ->children()
                ->arrayNode('panels')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('left')
                            ->prototype('scalar')->end()
                        ->end()
                        ->arrayNode('right')
                            ->prototype('scalar')->end()
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
