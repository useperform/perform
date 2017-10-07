<?php

namespace Perform\BaseBundle\Installer;

use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Process\Process;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class AssetsInstaller implements InstallerInterface
{
    public function install(ContainerInterface $container, LoggerInterface $logger)
    {
        $dir = __DIR__.'/..';
        NpmHelper::install($dir, $logger);
        ProcessHelper::run('npm run build', $logger, $dir);
    }

    public function requiresConfiguration()
    {
        return false;
    }
}
