<?php

namespace Perform\UserBundle\Installer;

use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Perform\BaseBundle\Installer\InstallerInterface;
use Perform\UserBundle\Importer\UserImporter;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class UsersInstaller implements InstallerInterface
{
    protected $importer;
    protected $usersFile;

    public function __construct(UserImporter $importer, $usersFile)
    {
        $this->importer = $importer;
        $this->usersFile = $usersFile;
    }

    public function install(LoggerInterface $logger)
    {
        if (!file_exists($this->usersFile)) {
            $logger->debug(sprintf('Not importing users from <info>%s</info>, file not found', $this->usersFile));
            return;
        }

        $logger->info(sprintf('Importing users from <info>%s</info>', $this->usersFile));
        $this->importer->importYamlFile($this->usersFile);
    }

    public function requiresConfiguration()
    {
        return true;
    }
}
