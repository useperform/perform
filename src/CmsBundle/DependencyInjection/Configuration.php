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
        $rootNode = $treeBuilder->root('perform_base');

        $rootNode
            ->children()
                ->arrayNode('block_types')
                    ->prototype('scalar')->end()
                ->end()
            ->end()
            ;

        return $treeBuilder;
    }
}
