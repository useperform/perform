<?php

namespace Admin\NotificationBundle\DependencyInjection;

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
        $rootNode = $treeBuilder->root('admin_notification');

        $rootNode
            ->children()
                ->arrayNode('default_publishers')
                    ->performNoDeepMerging()
                    ->prototype('scalar')
                ->end()
            ->end();

        return $treeBuilder;
    }
}
