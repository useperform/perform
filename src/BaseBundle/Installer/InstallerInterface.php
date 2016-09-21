<?php

namespace Perform\BaseBundle\Installer;

use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * InstallerInterface.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
interface InstallerInterface
{
    public function install(ContainerInterface $container, LoggerInterface $logger);
}
