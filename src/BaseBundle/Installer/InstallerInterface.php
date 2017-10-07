<?php

namespace Perform\BaseBundle\Installer;

use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
interface InstallerInterface
{
    /**
     * Run the installation.
     *
     * @param ContainerInterface $container
     * @param LoggerInterface    $logger
     */
    public function install(ContainerInterface $container, LoggerInterface $logger);

    /**
     * @return bool
     */
    public function requiresConfiguration();
}
