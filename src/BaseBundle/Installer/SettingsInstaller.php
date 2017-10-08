<?php

namespace Perform\BaseBundle\Installer;

use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Import settings definitions from different bundles.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class SettingsInstaller implements InstallerInterface, BundleAwareInstallerInterface
{
    public function install(ContainerInterface $container, LoggerInterface $logger)
    {
        $this->installBundles($container, $logger, []);
    }

    public function installBundles(ContainerInterface $container, LoggerInterface $logger, array $bundles)
    {
        $searcher = $container->get('perform_base.bundle_searcher');
        $importer = $container->get('perform_base.settings_importer');
        foreach ($searcher->findResourcesAtPath('config/settings.yml', $bundles) as $file) {
            $logger->info('Importing '.$file);
            $importer->importYamlFile($file);
        }
    }

    public function requiresConfiguration()
    {
        return true;
    }
}
