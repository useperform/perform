<?php

namespace Perform\MediaBundle\Installer;

use Perform\BaseBundle\Installer\InstallerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Perform\BaseBundle\Installer\NpmHelper;
use Perform\BaseBundle\Installer\ProcessHelper;

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
