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
            ->fixXmlConfig('preference')
            ->children()
                ->scalarNode('active_recipient_provider')
                ->end()
                ->arrayNode('email')
                    ->children()
                        ->arrayNode('default_from')
                            ->useAttributeAsKey('email')
                            ->prototype('scalar')->end()
                            ->requiresAtLeastOneElement()
                            ->info("['noreply@example.com': 'Sender'] or ['noreply@example.com'] or ['noreply@example.com': 'Sender', 'noreply2@example.com': 'Another Sender']")
                        ->end()
                    ->end()
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
                ->end()
                ->arrayNode('preferences')
                    ->useAttributeAsKey('name')
                    ->prototype('array')
                    ->children()
                        ->enumNode('type')
                            ->values(['static', 'settings', 'service'])
                            ->isRequired()
                        ->end()
                        ->scalarNode('prefix')->end()
                        ->booleanNode('default')->defaultValue(true)->end()
                        ->scalarNode('service')->end()
                    ->end()
                    ->validate()
                        ->ifTrue(function($v) {
                            return $v['type'] === 'settings' && !isset($v['prefix']);
                        })
                        ->thenInvalid('The "settings" notification preference requires the "prefix" option.')
                        ->ifTrue(function($v) {
                            return $v['type'] === 'service' && !isset($v['service']);
                        })
                        ->thenInvalid('The "service" notification preference requires the "service" option.')
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
