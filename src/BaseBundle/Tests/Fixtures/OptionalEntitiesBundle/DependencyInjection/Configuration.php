<?php

namespace Perform\BaseBundle\Tests\Fixtures\OptionalEntitiesBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('optional_entities');

        $rootNode
            ->children()
                ->booleanNode('three')->end()
                ->booleanNode('four')->end()
            ->end()
        ->end();

        return $treeBuilder;
    }
}
