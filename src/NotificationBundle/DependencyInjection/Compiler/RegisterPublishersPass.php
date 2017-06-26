<?php

namespace Perform\NotificationBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Perform\NotificationBundle\Notifier\TraceableNotifier;

/**
 * Register additional notification publishers automatically.
 **/
class RegisterPublishersPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $definition = $container->getDefinition('perform_notification.notifier');

        foreach ($container->findTaggedServiceIds('perform_notification.publisher') as $service => $tag) {
            $definition->addMethodCall('addPublisher', [new Reference($service)]);
        }

        if ($container->hasDefinition('profiler')) {
            $container->getDefinition('perform_notification.notifier')
                ->setClass(TraceableNotifier::class);
        }
    }
}
