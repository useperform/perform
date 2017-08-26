<?php

namespace Perform\UserBundle\Installer;

use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * UsersInstaller.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class UsersInstaller implements InstallerInterface
{
    public function install(ContainerInterface $container, LoggerInterface $logger)
    {
        $file = $container->getParameter('kernel.root_dir').'/config/perform_users.yml';
        if (!file_exists($file)) {
            return;
        }

        $logger->info(sprintf('Importing users from <info>%s</info>', $file));
        $importer = $container->get('perform_user.importer.user');
        $importer->importYamlFile($file);
    }
}
