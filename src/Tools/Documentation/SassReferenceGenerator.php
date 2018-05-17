<?php

namespace Perform\Tools\Documentation;

use Twig\Environment;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\FileNotFoundException;

/**
 * Document the available sass variables for perform stylesheets.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class SassReferenceGenerator
{
    protected $twig;
    protected $fs;

    public function __construct(Environment $twig)
    {
        $this->twig = $twig;
        $this->fs = new FileSystem();
    }

    public function generateFile($source, $target)
    {
        $this->fs->dumpFile($target, $this->generate($source));
    }

    public function generate($source)
    {
        if (!$this->fs->exists($source)) {
            throw new FileNotFoundException(sprintf('Sass variables file "%s" was not found.', $source));
        }
        $vars = [];
        $inComment = false;
        $currentDoc = '';

        $file = new \SplFileObject($source);
        foreach ($file as $line) {
            $line = trim($line);
            if (substr($line, 0, 2) === '/*') {
                $inComment = true;
                $line = trim(substr($line, 2));
            }
            if ($inComment) {
                $currentDoc .= rtrim($line, '*/').PHP_EOL;
                if (substr($line, -2) === '*/') {
                    $inComment = false;
                }

                continue;
            }

            if (substr($line, 0, 1) === '$') {
                $inComment = false;
                preg_match('/(\\$[-a-z0-9]+) *: *(.*)!default/', $line, $matches);
                if (count($matches) !== 3) {
                    throw new \RuntimeException(sprintf('Unable to parse sass variable line: "%s". Each variable declaration must end with !default.', $line));
                }

                $vars[] = [
                    'name' => $matches[1],
                    'default' => trim($matches[2]),
                    'doc' => trim($currentDoc),
                ];
                $currentDoc = '';
            }

            usort($vars, function($a, $b) {
                if ($a['name'] === $b['name']) {
                    return 0;
                }

                return $a['name'] < $b['name'] ? -1 : 1;
            });
        }

        return $this->twig->render('sass_reference.rst.twig', [
            'sass_vars' => $vars,
        ]);
    }
}
