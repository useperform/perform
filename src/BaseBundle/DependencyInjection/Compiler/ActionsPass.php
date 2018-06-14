<?php

namespace Perform\BaseBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Perform\BaseBundle\DependencyInjection\LoopableServiceLocator;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class ActionsPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        foreach ($container->findTaggedServiceIds('perform_base.action') as $service => $tag) {
            foreach ($tag as $item) {
                if (!isset($item['alias'])) {
                    throw new \InvalidArgumentException(sprintf('The service "%s" tagged with "perform_base.action" must set the "alias" option in the tag.', $service));
                }
                $actions[$item['alias']] = new Reference($service);
            }
        }
        $container->getDefinition('perform_base.action_registry')
            ->setArgument(0, LoopableServiceLocator::createDefinition($actions));
    }
}
