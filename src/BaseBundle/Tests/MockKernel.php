<?php

namespace Perform\BaseBundle\Tests;

use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;

/**
 * MockKernel
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class MockKernel extends Kernel
{
    protected $bundles = [];

    public function __construct()
    {
    }

    public function addBundle(BundleInterface $bundle)
    {
        $this->bundles[$bundle->getName()] = $bundle;
    }

    public function getBundles()
    {
        return $this->bundles;
    }

    public function registerBundles()
    {
        return [];
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
    }
}
