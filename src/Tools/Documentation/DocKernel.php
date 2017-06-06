<?php

namespace Perform\Tools\Documentation;

use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;

/**
 * DocKernel.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class DocKernel extends Kernel
{
    protected $dir;

    public function __construct($dir)
    {
        $this->dir = $dir;
    }

    public function registerBundles()
    {
        return [
            new \Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new \Symfony\Bundle\SecurityBundle\SecurityBundle(),
            new \Symfony\Bundle\TwigBundle\TwigBundle(),
            new \Symfony\Bundle\MonologBundle\MonologBundle(),
            new \Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle(),
            new \Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
            new \Doctrine\Bundle\DoctrineCacheBundle\DoctrineCacheBundle(),
            new \Doctrine\Bundle\MigrationsBundle\DoctrineMigrationsBundle(),
            new \Stof\DoctrineExtensionsBundle\StofDoctrineExtensionsBundle(),
            new \Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle(),
            new \Oneup\FlysystemBundle\OneupFlysystemBundle(),
            new \Knp\Bundle\MenuBundle\KnpMenuBundle(),

            new \Perform\BaseBundle\PerformBaseBundle(),
            new \Perform\NotificationBundle\PerformNotificationBundle(),
            new \Perform\MediaBundle\PerformMediaBundle(),
        ];
    }

    public function getRootDir()
    {
        return $this->dir;
    }

    public function getCacheDir()
    {
        return $this->dir;
    }

    public function getLogDir()
    {
        return $this->dir;
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(__DIR__.'/doc_config.yml');
    }
}
