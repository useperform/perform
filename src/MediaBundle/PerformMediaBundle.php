<?php

namespace Perform\MediaBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Perform\MediaBundle\DependencyInjection\Compiler\RegisterFilePluginsPass;

/**
 * PerformMediaBundle
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class PerformMediaBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);
        $container->addCompilerPass(new RegisterFilePluginsPass());
    }
}
