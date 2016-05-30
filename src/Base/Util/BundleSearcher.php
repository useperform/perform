<?php

namespace Admin\Base\Util;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Finder\Finder;

/**
 * BundleSearcher
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
        return $this->findItemsInNamespaceSegment($namespaceSegment, function($class) {
            return $class;
        });
    }

    /**
     * @param string $namespace The sub-namespace, e.g. 'Entity' or 'Admin\Type'
     */
    public function findItemsInNamespaceSegment($namespaceSegment, \Closure $mapper)
    {
        $namespaceSegment = trim($namespaceSegment, '/\\');
        $bundles = $this->container->getParameter('kernel.bundles');
        $items = [];

        foreach ($bundles as $bundleName => $bundleClass) {
            $reflection = new \ReflectionClass($bundleClass);
            $dirname = dirname($reflection->getFileName());
            $namespace = $reflection->getNamespaceName().'\\'.$namespaceSegment.'\\';

            if (!is_dir($dir = $dirname.'/'.$namespaceSegment)) {
                continue;
            }
            foreach (Finder::create()->files()->in($dir)->name('*.php') as $file) {
                $class = $namespace.$file->getBasename('.php');
                if (!class_exists($class)) {
                    continue;
                }

                $items[$class] = $mapper($class, $file->getBasename('.php'), $bundleName, $bundleClass);
            }
        }

        return $items;
    }
}
