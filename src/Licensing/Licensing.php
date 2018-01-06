<?php

namespace Perform\Licensing;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Perform\Licensing\EventListener\LicensingListener;
use Perform\Licensing\Util\PackageUtil;
use Perform\Licensing\KeyChecker;

/**
 * Thank you for choosing to use Perform for your application!
 *
 * As a customer, you are welcome to browse through the source code to
 * see how things work.
 *
 * It's fairly simple to subvert this licensing code, but please
 * consider saving your time and purchasing a license instead.
 *
 * Remember that your support helps fund future development.
 *
 * Thank you.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class Licensing
{
    const PARAM_PROJECT_KEY = 'perform.project_key';
    const PARAM_LOGGER_SERVICE = 'perform.licensing.logger';
    const LISTENER_SERVICE = 'perform.licensing.listener';

    public static function validateProject(ContainerBuilder $container)
    {
        if ($container->getParameter('kernel.debug') || $container->hasDefinition(self::LISTENER_SERVICE)) {
            return;
        }

        $key = $container->hasParameter(self::PARAM_PROJECT_KEY) ?
             $container->getParameter(self::PARAM_PROJECT_KEY) : '';

        $checker = new KeyChecker('https://useperform.com/api/validate', $container->getParameter('kernel.bundles'), self::getPerformVersions($container));
        $response = $checker->validate($key);

        $logger = $container->hasParameter(self::PARAM_LOGGER_SERVICE) ?
                $container->getParameter(self::PARAM_LOGGER_SERVICE) : 'logger';
        $def = $container->register(self::LISTENER_SERVICE, LicensingListener::class);
        $def->setArguments([new Reference($logger), $key, $response->isValid(), $response->getDomains()]);
        $def->addTag('kernel.event_listener', [
            'event' => 'kernel.request',
            'method' => 'onKernelRequest',
        ]);
    }

    private static function getPerformVersions(ContainerBuilder $container)
    {
        try {
            $projectDir = $container->hasParameter('kernel.project_dir') ?
                        $container->getParameter('kernel.project_dir') :
                        $container->getParameter('kernel.root_dir').'/../';

            return PackageUtil::getPerformVersions([
                $projectDir.'/composer.lock',
            ]);
        } catch (\RuntimeException $e) {
            return [];
        }
    }
}
