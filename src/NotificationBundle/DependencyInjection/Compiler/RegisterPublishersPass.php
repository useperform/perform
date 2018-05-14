<?php

namespace Perform\NotificationBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Perform\NotificationBundle\Notifier\TraceableNotifier;
use Perform\NotificationBundle\Publisher\EmailPublisher;
use Perform\BaseBundle\DependencyInjection\LoopableServiceLocator;
use Perform\BaseBundle\Util\StringUtil;

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
                $tag[0]['alias'] = $this->guessPublisherAlias($container, $service);
                $msg = sprintf(
                    'Auto generating the name "%s" for notification publisher service "%s". To set the name explicitly, make sure it has a "perform_notification.publisher" tag with the "alias" option set.',
                    $tag[0]['alias'],
                    $service
                );
                $container->log($this, $msg);
            }
            $name = $tag[0]['alias'];

            $publishers[$name] = new Reference($service);
        }

        $notifier->setArgument(0, LoopableServiceLocator::createDefinition($publishers));

        if ($container->hasDefinition('profiler')) {
            $notifier->setClass(TraceableNotifier::class);
        } else {
            $container->removeDefinition('perform_notification.data_collector');
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

    private function guessPublisherAlias(ContainerBuilder $container, $service)
    {
        $definition = $container->getDefinition($service);

        return strtolower(StringUtil::classBasename($definition->getClass(), 'Publisher'));
    }
}
