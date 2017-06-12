<?php

namespace Perform\DevBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Perform\DevBundle\DependencyInjection\Compiler\AddFrontendsPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * PerformDevBundle.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class PerformDevBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);
        $container->addCompilerPass(new AddFrontendsPass());
    }
}
