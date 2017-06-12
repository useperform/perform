<?php

namespace Perform\BaseBundle\Installer;

use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
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
        $searcher = $container->get('perform_base.bundle_searcher');
        $files = $searcher->findResourcesAtPath('../install_assets.sh');
        if (empty($files)) {
            return;
        }

        $this->checkRequirements($logger);

        foreach ($files as $file) {
            $logger->info('Running '.$file);

            $process = new Process(sprintf('(cd %s && %s)', dirname($file), $file));
            $process->setTimeout(300);
            $process->mustRun(function ($type, $buffer) use ($logger) {
                if (Process::ERR === $type) {
                    $logger->warning($buffer);
                } else {
                    $logger->info($buffer);
                }
            });
        }
    }

    protected function checkRequirements(LoggerInterface $logger)
    {
        $process = new Process('which npm');
        if ($process->run() !== 0) {
            throw new \RuntimeException('Npm executable not found. Please install nodejs and npm before continuing.');
        }

        $process = new Process('which yarn');
        if ($process->run() !== 0) {
            $logger->warning('Yarn executable not found. Consider installing yarn with "npm install -g yarn" to speed up asset building.'.PHP_EOL);
        }
    }
}
