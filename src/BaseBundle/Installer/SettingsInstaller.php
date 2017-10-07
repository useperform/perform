<?php

namespace Perform\BaseBundle\Installer;

use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * SettingsInstaller.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class SettingsInstaller implements InstallerInterface
{
    public function install(ContainerInterface $container, LoggerInterface $logger)
    {
        $searcher = $container->get('perform_base.bundle_searcher');
        $importer = $container->get('perform_base.settings_importer');
        foreach ($searcher->findResourcesAtPath('config/settings.yml') as $file) {
            $logger->info('Importing '.$file);
            $importer->importYamlFile($file);
        }
    }

    public function requiresConfiguration()
    {
        return true;
    }
}
