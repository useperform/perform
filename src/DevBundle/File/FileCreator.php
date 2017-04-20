<?php

namespace Perform\DevBundle\File;

use Perform\DevBundle\Exception\FileException;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;

/**
 * FileCreator.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class FileCreator
{
    protected $fs;
    protected $twig;

    public function __construct(Filesystem $fs, \Twig_Environment $twig)
    {
        $this->fs = $fs;
        $this->twig = $twig;
    }

    public function create($file, $template, array $vars = [])
    {
        if ($this->fs->exists($file)) {
            throw new FileException($file.' exists.');
        }

        return $this->forceCreate($file, $template, $vars);
    }

    public function forceCreate($file, $template, array $vars = [])
    {
        return $this->fs->dumpFile($file, $this->twig->render('PerformDevBundle:skeletons:'.$template, $vars));
    }

    public function resolveBundleClass(BundleInterface $bundle, $relativeClass, array $vars = [])
    {
        $relativeClass = trim($relativeClass, '\\');
        $classname = sprintf('%s\\%s', $bundle->getNamespace(), $relativeClass);
        $file = sprintf('%s/%s.php', $bundle->getPath(), str_replace('\\', '/', $relativeClass));
        $classBasename = substr(basename($file), 0, -4);
        $namespace = sprintf('%s\\%s', $bundle->getNamespace(), str_replace('/', '\\', dirname(str_replace('\\', '/', $relativeClass))));

        $vars['classname'] = $classBasename;
        $vars['namespace'] = $namespace;

        return [$file, $vars];
    }
}
