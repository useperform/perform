<?php

namespace Perform\DevBundle;

use Perform\DevBundle\DependencyInjection\Compiler\NpmDependenciesPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class PerformDevBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);
        $container->addCompilerPass(new NpmDependenciesPass());
    }
}
