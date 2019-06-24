<?php

namespace Perform\UserBundle\Installer;

use Psr\Log\LoggerInterface;
use Perform\BaseBundle\Installer\InstallerInterface;
use Perform\UserBundle\Importer\UserImporter;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class UsersInstaller implements InstallerInterface
{
    protected $importer;
    protected $userDefinitions = [];

    public function __construct(UserImporter $importer, array $userDefinitions)
    {
        $this->importer = $importer;
        $this->userDefinitions = $userDefinitions;
    }

    public function install(LoggerInterface $logger)
    {
        $count = count($this->userDefinitions);
        if ($count < 1) {
            $logger->debug('No initial users found in configuration');
            return;
        }

        $logger->info(sprintf('Importing <info>%s</info> %s from configuration', $count, $count === 1 ? 'user' : 'users'));
        $this->importer->import($this->userDefinitions);
    }
}
