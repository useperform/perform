<?php

namespace Perform\BaseBundle\Installer;

use Psr\Log\LoggerInterface;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class AssetsInstaller implements InstallerInterface
{
    public function install(LoggerInterface $logger)
    {
        $dir = __DIR__.'/../Resources';
        NpmHelper::install($dir, $logger);
        ProcessHelper::run('npm run build', $logger, $dir);
    }
}
