<?php

namespace Perform\MailingListBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Perform\MailingListBundle\Enricher\UserEnricher;

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

        $enrichers = [];
        foreach ($container->findTaggedServiceIds('perform_mailing_list.enricher') as $service => $tag) {
            $enrichers[] = new Reference($service);
        }
        $container->getDefinition('perform_mailing_list.manager')
            ->setArgument(2, $enrichers);
    }
}
