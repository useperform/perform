<?php

namespace Perform\NotificationBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Perform\NotificationBundle\Notifier\TraceableNotifier;
use Perform\NotificationBundle\Publisher\EmailPublisher;
use Perform\BaseBundle\DependencyInjection\LoopableServiceLocator;

/**
 * Register additional notification publishers automatically.
 **/
class RegisterPublishersPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $this->maybeRemoveEmailPublisher($container);

        $notifier = $container->getDefinition('perform_notification.notifier');

        $publishers = [];
        foreach ($container->findTaggedServiceIds('perform_notification.publisher') as $service => $tag) {
            if (!isset($tag[0]['alias'])) {
                throw new \InvalidArgumentException(sprintf('The service "%s" tagged with "perform_notification.publisher" must set the "alias" option in the tag.', $service));
            }
            $name = $tag[0]['alias'];

            $publishers[$name] = new Reference($service);
        }

        $notifier->setArgument(0, LoopableServiceLocator::createDefinition($publishers));

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
