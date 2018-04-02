<?php

namespace Perform\MailingListBundle\DependencyInjection\Compiler;

use Perform\BaseBundle\DependencyInjection\LoopableServiceLocator;
use Perform\MailingListBundle\Enricher\UserEnricher;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Find connectors and enrichers to add to the subscriber manager.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class ConfigureManagerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('perform_user.repo.user')) {
            $msg = sprintf('Removing the %s enricher; the PerformUserBundle is not registered.', UserEnricher::class);
            $container->log($this, $msg);
            $container->removeDefinition('perform_mailing_list.enricher.user');
        }

        $connectors = [];
        foreach ($container->findTaggedServiceIds('perform_mailing_list.connector') as $service => $tag) {
            if (!isset($tag[0]['alias'])) {
                throw new \InvalidArgumentException(sprintf('The service "%s" tagged with "perform_mailing_list.connector" must set the "alias" option in the tag.', $service));
            }

            $connectors[$tag[0]['alias']] = new Reference($service);
        }

        $container->getDefinition('perform_mailing_list.manager')
            ->setArgument(1, LoopableServiceLocator::createDefinition($connectors));

        $enrichers = [];
        foreach ($container->findTaggedServiceIds('perform_mailing_list.enricher') as $service => $tag) {
            $enrichers[] = new Reference($service);
        }
        $container->getDefinition('perform_mailing_list.manager')
            ->setArgument(2, LoopableServiceLocator::createDefinition($enrichers));
    }
}
