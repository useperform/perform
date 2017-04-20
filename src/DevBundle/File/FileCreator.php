<?php

namespace Perform\DevBundle\File;

use Perform\DevBundle\Exception\FileException;
use Symfony\Component\Filesystem\Filesystem;

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
}
