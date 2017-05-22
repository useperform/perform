<?php

namespace Perform\BaseBundle\Util;

use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpKernel\KernelInterface;

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
     * Get the named bundle instances.
     *
     * @param array $bundleNames
     *
     * @return BundleInterface[]
     */
    public function getBundles(array $bundleNames)
    {
        $bundles = $this->kernel->getBundles();
        if (empty($bundleNames)) {
            return $bundles;
        }

        return array_filter(
            $bundles,
            function ($bundleName) use ($bundleNames) {
                return in_array($bundleName, $bundleNames);
            },
            ARRAY_FILTER_USE_KEY);
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
     * @param string       $namespaceSegment The sub-namespace within a bundle, e.g. 'Entity' or 'Form\Type'
     * @param Closure|null $mapper           An optional mapper function taking the classname, classBasename, and bundle instance
     * @param array        $bundleNames      An array of bundle names to use. If empty, use all bundles
     */
    public function findClassesWithNamespaceSegment($namespaceSegment, \Closure $mapper = null, array $bundleNames = [])
    {
        $namespaceSegment = trim($namespaceSegment, '/\\');
        $classes = [];

        foreach ($this->getBundles($bundleNames) as $bundle) {
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
     * @param string $path        The path within Resources/, e.g. 'config/settings.yml'
     * @param array  $bundleNames An array of bundle names to use. If empty, use all bundles.
     *
     * @return Finder
     */
    public function findResourcesAtPath($path, array $bundleNames = [])
    {
        $bundles = $this->getBundles($bundleNames);
        $path = trim($path, '/');

        $finder = Finder::create()
                ->files()
                ->name(basename($path));

        foreach ($bundles as $bundle) {
            $dir = $bundle->getPath().'/Resources/'.dirname($path);
            if (is_dir($dir)) {
                $finder->in($dir);
            }
        }

        return $finder;
    }
}
