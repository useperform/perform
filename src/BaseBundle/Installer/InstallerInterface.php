<?php

namespace Perform\BaseBundle\Installer;

use Psr\Log\LoggerInterface;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
interface InstallerInterface
{
    /**
     * Run the installation.
     *
     * @param LoggerInterface $logger
     */
    public function install(LoggerInterface $logger);
}
