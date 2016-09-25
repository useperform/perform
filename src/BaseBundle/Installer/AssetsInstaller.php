<?php

namespace Perform\BaseBundle\Installer;

use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Perform\BaseBundle\Util\BundleSearcher;

/**
 * AssetsInstaller.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class AssetsInstaller implements InstallerInterface
{
    public function install(ContainerInterface $container, LoggerInterface $logger)
    {
        $searcher = new BundleSearcher($container);
        foreach ($searcher->findResourcesAtPath('../install_assets.sh') as $file) {
            $logger->info('Running '.$file);
            passthru(sprintf('(cd %s && %s)', dirname($file), $file));
        }
    }
}
