<?php

namespace Perform\Base\DependencyInjection;

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
        $rootNode = $treeBuilder->root('admin_base');

        $rootNode
            ->children()
                ->arrayNode('mailer')
                    ->children()
                        ->arrayNode('excluded_domains')
                            ->prototype('scalar')->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('admins')
                    ->prototype('array')
                        ->prototype('array')
                            ->prototype('array')
                                ->prototype('scalar')
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('panels')
                    ->children()
                        ->arrayNode('left')
                            ->prototype('scalar')->end()
                        ->end()
                        ->arrayNode('right')
                            ->prototype('scalar')->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('menu')
                    ->children()
                        ->arrayNode('order')
                            ->prototype('scalar')->end()
                        ->end()
                    ->end()
                ->end()
            ->end();

        // how to configure admin options:
        // admins:
        //     AdminTeamBundle:TeamMember:
        //         fieldOptions:
        //             role:
        //                 label: 'ROLE ID'

        return $treeBuilder;
    }
}
