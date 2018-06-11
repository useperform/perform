<?php

namespace Perform\BaseBundle\Test;

use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;
use Temping\Temping;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class TestKernel extends Kernel
{
    protected $temping;
    protected $extraBundles;
    protected $extraConfigs = [];

    public function __construct(array $extraBundles = [], array $extraConfigs = [])
    {
        parent::__construct('dev', true);
        $this->temping = new Temping();
        $this->rootDir = $this->temping->getDirectory();
        $this->extraBundles = $extraBundles;
        $this->extraConfigs = $extraConfigs;
    }

    public function shutdown()
    {
        $this->temping->reset();
        parent::shutdown();
    }

    /**
     * Required to be unique so the kernel can be reused without conflicting with other tests.
     */
    public function getContainerClass()
    {
        return 'Test'.uniqid().'Container';
    }

    public function getProjectDir()
    {
        return $this->rootDir;
    }

    public function registerBundles()
    {
        return array_merge([
            new \Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new \Symfony\Bundle\SecurityBundle\SecurityBundle(),
            new \Symfony\Bundle\TwigBundle\TwigBundle(),
            new \Symfony\Bundle\MonologBundle\MonologBundle(),
            new \Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle(),
            new \Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
            new \Doctrine\Bundle\DoctrineCacheBundle\DoctrineCacheBundle(),
            new \Stof\DoctrineExtensionsBundle\StofDoctrineExtensionsBundle(),
            new \Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle(),
            new \Knp\Bundle\MenuBundle\KnpMenuBundle(),

            new \Perform\BaseBundle\PerformBaseBundle(),
            new \Perform\NotificationBundle\PerformNotificationBundle(),
        ], $this->extraBundles);
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(__DIR__.'/config.yml');
        foreach ($this->extraConfigs as $config) {
            $loader->load($config);
        }
    }
}
