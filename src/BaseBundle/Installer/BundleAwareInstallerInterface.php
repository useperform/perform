<?php

namespace Perform\BaseBundle\Installer;

use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Represents an installer that uses resources from multiple bundles.
 *
 * For example, an installer may read from a file in the Resources/
 * folder of the given bundles during installation.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
interface BundleAwareInstallerInterface
{
    /**
     * Run the installation in the given bundles.
     *
     * @param ContainerInterface $container
     * @param LoggerInterface    $logger
     * @param BundleInterface[]  $bundles
     */
    public function installBundles(ContainerInterface $container, LoggerInterface $logger, array $bundles);
}
