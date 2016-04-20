<?php

namespace Admin\NotificationBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Register additional notification publishers automatically.
 **/
class RegisterPublishersPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $definition = $container->getDefinition('admin_notification.notifier');

        foreach ($container->findTaggedServiceIds('admin_notification.publisher') as $service => $tag) {
            $definition->addMethodCall('addPublisher', [new Reference($service)]);
        }
    }
}
