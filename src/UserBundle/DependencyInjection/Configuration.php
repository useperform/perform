<?php

namespace Perform\UserBundle\DependencyInjection;

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
        $rootNode = $treeBuilder->root('perform_user');

        $rootNode
            ->children()
                ->integerNode('reset_token_expiry')
                    ->info('Number of seconds after generation.')
                    ->defaultValue(1800)
                ->end()
            ->end();

        return $treeBuilder;
    }
}
