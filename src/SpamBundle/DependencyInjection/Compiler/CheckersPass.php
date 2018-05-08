<?php

namespace Perform\SpamBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Perform\BaseBundle\DependencyInjection\LoopableServiceLocator;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class CheckersPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $textCheckers = array_map(function ($service) {
            return new Reference($service);
        }, array_keys($container->findTaggedServiceIds('perform_spam.text_checker')));

        $formCheckers = array_map(function ($service) {
            return new Reference($service);
        }, array_keys($container->findTaggedServiceIds('perform_spam.form_checker')));

        $requestCheckers = array_map(function ($service) {
            return new Reference($service);
        }, array_keys($container->findTaggedServiceIds('perform_spam.request_checker')));

        $container->getDefinition('perform_spam.manager')
            ->setArgument(0, LoopableServiceLocator::createDefinition($textCheckers))
            ->setArgument(1, LoopableServiceLocator::createDefinition($formCheckers))
            ->setArgument(2, LoopableServiceLocator::createDefinition($requestCheckers));
    }
}
