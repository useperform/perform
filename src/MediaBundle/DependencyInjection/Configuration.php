<?php

namespace Perform\MediaBundle\DependencyInjection;

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
        $rootNode = $treeBuilder->root('perform_media');

        $rootNode
            ->fixXmlConfig('bucket')
            ->children()
                ->arrayNode('buckets')
                    ->useAttributeAsKey('name')
                    ->prototype('array')
                        ->children()
                            ->scalarNode('flysystem')->isRequired()->end()
                            ->scalarNode('url_generator')->isRequired()->end()
                            ->arrayNode('types')
                                ->prototype('array')
                                    ->children()
                                        ->scalarNode('service')
                                        ->end()
                                        ->scalarNode('type')
                                        ->end()
                                        ->enumNode('engine')
                                            ->values(['gd', 'imagick', 'gmagick'])
                                            ->defaultValue('gd')
                                        ->end()
                                        ->arrayNode('widths')
                                            ->prototype('scalar')
                                        ->end()
                                    ->end()
                                ->end()
                                ->validate()
                                    ->ifTrue(function($v) {
                                        return !isset($v['type']) && !isset($v['service']);
                                    })
                                    ->thenInvalid('Each media type must specify a type or a service.')
                                ->end()
                            ->end()
                            ->defaultValue([['type' => 'other']])
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
