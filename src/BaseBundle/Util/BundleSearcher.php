<?php

namespace Perform\BaseBundle\Util;

use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;

/**
 * Find classes and resources in bundles.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class BundleSearcher
{
    protected $kernel;

    public function __construct(KernelInterface $kernel)
    {
        $this->kernel = $kernel;
    }

    /**
     * Get a filtered array of registered bundles that are in the $bundles array.
     *
     * $bundles may be an array of bundle names or an array of BundleInterface instances.
     *
     * @param array|BundleInterface[] $bundles An array of bundles to use. If empty, use all bundles
     *
     * @return BundleInterface[]
     */
    public function getBundles(array $bundles)
    {
        $availableBundles = $this->kernel->getBundles();
        if (empty($bundles)) {
            return $availableBundles;
        }

        return array_filter($availableBundles, function ($bundle) use ($bundles) {
            return in_array($bundle->getName(), $bundles) || in_array($bundle, $bundles, true);
        });
    }

    /**
     * Get an array of classes in a namespace segment. The keys and
     * values will be the classname.
     *
     * If a mapper function is supplied, the result of the mapper
     * function will be used as the value instead of the classname.
     *
     * If the mapper function returns false, skip that classname entirely from the array.
     *
     * $bundles may be an array of bundle names or an array of BundleInterface instances.
     *
     * @param string       $namespaceSegment The sub-namespace within a bundle, e.g. 'Entity' or 'Form\Type'
     * @param Closure|null $mapper           An optional mapper function taking the classname, classBasename, and bundle instance
     * @param array|BundleInterface[] $bundles An array of bundles to use. If empty, use all bundles
     */
    public function findClassesWithNamespaceSegment($namespaceSegment, \Closure $mapper = null, array $bundles = [])
    {
        $namespaceSegment = trim($namespaceSegment, '/\\');
        $classes = [];

        foreach ($this->getBundles($bundles) as $bundle) {
            $dirname = $bundle->getPath();
            $namespace = $bundle->getNamespace().'\\'.$namespaceSegment.'\\';

            if (!is_dir($dir = $dirname.'/'.str_replace('\\', '/', $namespaceSegment))) {
                continue;
            }
            foreach (Finder::create()->files()->in($dir)->name('*.php') as $file) {
                $classBasename = $file->getBasename('.php');
                $class = $namespace.$classBasename;
                if (!class_exists($class)) {
                    continue;
                }

                if (!$mapper) {
                    $classes[$class] = $class;
                    continue;
                }

                $result = $mapper($class, $classBasename, $bundle);
                if ($result !== false) {
                    $classes[$class] = $result;
                }
            }
        }

        return $classes;
    }

    /**
     * Get an iterator of file objects located at the given path with Resources/ of the given bundles.
     *
     * $bundles may be an array of bundle names or an array of BundleInterface instances.
     *
     * @param string $path        The path within Resources/, e.g. 'config/settings.yml'
     * @param array|BundleInterface[] $bundles An array of bundles to use. If empty, use all bundles
     *
     * @return Finder
     */
    public function findResourcesAtPath($path, array $bundles = [])
    {
        $path = trim($path, '/');

        $finder = Finder::create()
                ->files()
                ->name(basename($path));

        $dirFound = false;
        foreach ($this->getBundles($bundles) as $bundle) {
            $dir = $bundle->getPath().'/Resources/'.dirname($path);
            if (is_dir($dir)) {
                $finder->in($dir);
                $dirFound = true;
            }
        }

        if (!$dirFound) {
            throw new \LogicException(sprintf('The "Resources/%s" directory was not found in any of the given bundles.', dirname($path)));
        }

        return $finder;
    }
}
