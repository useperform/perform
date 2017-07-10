<?php

namespace Perform\NotificationBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Perform\NotificationBundle\Notifier\TraceableNotifier;
use Perform\NotificationBundle\Publisher\EmailPublisher;

/**
 * Register additional notification publishers automatically.
 **/
class RegisterPublishersPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('perform_base.email.mailer')) {
            $msg = sprintf(
                'Removing the %s publisher; the BaseBundle mailer has not been configured. Configure the perform_base.mailer node to use this publisher.',
                EmailPublisher::class);
            $container->log($this, $msg);
            $container->removeDefinition('perform_notification.publisher.email');
        }

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
