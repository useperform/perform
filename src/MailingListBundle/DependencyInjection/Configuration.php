<?php

namespace Perform\MailingListBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 */
class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('perform_mailing_list');

        $rootNode
            ->fixXmlConfig('connector')
            ->children()
                ->arrayNode('connectors')
                    ->useAttributeAsKey('name')
                    ->prototype('array')
                    ->children()
                        ->enumNode('connector')
                            ->values(['local', 'mailchimp'])
                            ->isRequired()
                        ->end()
                        ->scalarNode('entity_manager')->defaultNull()->end()
                        ->scalarNode('api_key')->end()
                    ->end()

                    ->validate()
                        ->ifTrue(function($v) { return $v['connector'] === 'mailchimp' && !isset($v['api_key']); })
                        ->thenInvalid('The mailchimp connector requires the "api_key" option.')
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
