<?php

namespace Perform\DevBundle\Npm;

/**
 * Declare a list of npm dependencies.
 *
 * Implementing classes may choose to return a different list depending on
 * certain conditions, e.g. only require a given package if a bundle
 * configuration option is enabled.
 **/
interface DependenciesInterface
{
    public function getDependencies(): array;
}
