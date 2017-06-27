<?php

namespace Perform\NotificationBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    protected $debug;

    public function __construct($debug)
    {
        $this->debug = $debug;
    }

    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('perform_notification');

        $rootNode
            ->children()
                ->scalarNode('active_recipient_provider')
                ->end()
                ->arrayNode('logging')
                    ->addDefaultsIfNotSet()
                    ->children()
                    ->booleanNode('enabled')
                        ->defaultValue($this->debug)
                    ->end()
                    ->scalarNode('level')
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
