<?php

namespace Perform\NotificationBundle\DependencyInjection;

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
        $rootNode = $treeBuilder->root('perform_notification');

        $rootNode
            ->children()
                ->arrayNode('default_publishers')
                    ->performNoDeepMerging()
                    ->prototype('scalar')
                    ->end()
                ->end()
                ->scalarNode('active_recipient_provider')
                ->end()
            ->end();

        return $treeBuilder;
    }
}
