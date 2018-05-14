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
        $this->maybeRemoveEmailPublisher($container);

        $definition = $container->getDefinition('perform_notification.notifier');

        foreach ($container->findTaggedServiceIds('perform_notification.publisher') as $service => $tag) {
            $definition->addMethodCall('addPublisher', [new Reference($service)]);
        }

        if ($container->hasDefinition('profiler')) {
            $container->getDefinition('perform_notification.notifier')
                ->setClass(TraceableNotifier::class);
        }
    }

    private function maybeRemoveEmailPublisher(ContainerBuilder $container)
    {
        $msg = sprintf('Removing the %s publisher; ', EmailPublisher::class);
        if (!$container->hasDefinition('swiftmailer.mailer.default')) {
            $msg .= 'swiftmailer has not been configured.';
        } elseif (empty($container->getParameter('perform_notification.email_default_from'))) {
            $msg .= 'the perform_notification.email.default_from configuration array is empty.';
        } else {
            return;
        }
        $container->log($this, $msg);
        $container->removeDefinition('perform_notification.publisher.email');
    }
}
