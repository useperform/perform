<?php

namespace Perform\DevBundle\File;

use Symfony\Component\HttpKernel\KernelInterface;

/**
 * KernelModifier.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class KernelModifier
{
    protected $kernel;

    public function __construct(KernelInterface $kernel)
    {
        $this->kernel = $kernel;
    }

    public function addBundle($bundleClass)
    {
        $ref = new \ReflectionObject($this->kernel);
        if (strpos(file_get_contents($ref->getFileName()), sprintf('new %s', $bundleClass))) {
            //already added
            return;
        }

        $lines = file($ref->getFileName());
        $method = $ref->getMethod('registerBundles');

        //search for the end of the first array in registerBundles
        //assume this method hasn't been modified too much from project-base, bail out otherwise
        //this isn't lisp!
        $methodLines = array_slice($lines, $method->getStartLine(), $method->getEndLine());
        foreach ($methodLines as $number => $line) {
            if (!preg_match('`\W+];`', $line)) {
                continue;
            }

            $spliceLine = $method->getStartLine() + $number;
            break;
        }
        if (!isset($spliceLine)) {
            throw new \Exception('Unable to add bundle to kernel.');
        }

        $bundleLine = sprintf("            new %s(),\n", $bundleClass);
        array_splice($lines, $spliceLine, 0, $bundleLine);
        file_put_contents($ref->getFileName(), implode('', $lines));
    }
}
