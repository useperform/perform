<?php

namespace Perform\BaseBundle\Installer;

use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Perform\BaseBundle\Util\BundleSearcher;
use Symfony\Component\Process\Process;

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

            $process = new Process(sprintf('(cd %s && %s)', dirname($file), $file));
            $process->mustRun(function ($type, $buffer) use ($logger) {
                if (Process::ERR === $type) {
                    $logger->warning($buffer);
                } else {
                    $logger->info($buffer);
                }
            });
        }
    }
}
