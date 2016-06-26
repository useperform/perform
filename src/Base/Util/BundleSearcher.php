<?php

namespace Admin\Base\Util;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Finder\Finder;

/**
 * BundleSearcher.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class BundleSearcher
{
    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @param string $namespace The sub-namespace, e.g. 'Entity' or 'Admin\Type'
     */
    public function findClassesInNamespaceSegment($namespaceSegment)
    {
        return $this->findItemsInNamespaceSegment($namespaceSegment, function ($class) {
            return $class;
        });
    }

    /**
     * @param string $namespace The sub-namespace, e.g. 'Entity' or 'Admin\Type'
     * @param Closure $mapper
     * @param array $bundleNames An array of bundle names to use. If empty, use all bundles.
     */
    public function findItemsInNamespaceSegment($namespaceSegment, \Closure $mapper, array $bundleNames = [])
    {
        $namespaceSegment = trim($namespaceSegment, '/\\');
        $items = [];

        foreach ($this->resolveBundles($bundleNames) as $bundleName => $bundleClass) {
            $reflection = new \ReflectionClass($bundleClass);
            $dirname = dirname($reflection->getFileName());
            $namespace = $reflection->getNamespaceName().'\\'.$namespaceSegment.'\\';

            if (!is_dir($dir = $dirname.'/'.str_replace('\\', '/', $namespaceSegment))) {
                continue;
            }
            foreach (Finder::create()->files()->in($dir)->name('*.php') as $file) {
                $class = $namespace.$file->getBasename('.php');
                if (!class_exists($class)) {
                    continue;
                }

                $item = $mapper($class, $file->getBasename('.php'), $bundleName, $bundleClass);
                if ($item !== false) {
                    $items[$class] = $item;
                }
            }
        }

        return $items;
    }

    /**
     * @param string $path The path within Resources/, e.g. 'config/settings.yml'
     *
     * @return \SplFileObject[]
     */
    public function findResourcesAtPath($path)
    {
        $files = [];
        $path = trim($path, '/');
        $bundles = $this->container->getParameter('kernel.bundles');

        foreach ($bundles as $bundleName => $bundleClass) {
            $reflection = new \ReflectionClass($bundleClass);
            $bundleDir = dirname($reflection->getFileName());

            if (!is_dir($dir = $bundleDir.'/Resources/'.dirname($path))) {
                continue;
            }
            foreach (Finder::create()->files()->in($dir)->name(basename($path)) as $file) {
                $files[] = $file->getPathname();
            }
        }

        return $files;
    }

    protected function resolveBundles(array $bundleNames)
    {
        $bundles = $this->container->getParameter('kernel.bundles');
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
}
