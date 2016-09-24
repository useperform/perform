<?php

namespace Perform\TwitterBundle\DependencyInjection;

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
        $rootNode = $treeBuilder->root('perform_twitter');

        $rootNode
            ->children()
                ->scalarNode('screen_name')
                    ->isRequired()
                    ->cannotBeEmpty()
                ->end()
                ->scalarNode('cache_provider')
                    ->isRequired()
                    ->cannotBeEmpty()
                ->end()
                ->integerNode('cache_ttl')
                    ->defaultValue(600)
                ->end()
                ->enumNode('credentials_source')
                    ->values(['config', 'settings'])
                    ->defaultValue('config')
                ->end()
                ->arrayNode('credentials')
                    ->children()
                        ->scalarNode('consumer_key')->cannotBeEmpty()->end()
                        ->scalarNode('consumer_secret')->cannotBeEmpty()->end()
                        ->scalarNode('access_token')->cannotBeEmpty()->end()
                        ->scalarNode('access_token_secret')->cannotBeEmpty()->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
