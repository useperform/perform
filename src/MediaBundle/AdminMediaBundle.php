<?php

namespace Admin\MediaBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Admin\MediaBundle\DependencyInjection\Compiler\RegisterFilePluginsPass;

/**
 * AdminMediaBundle
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class AdminMediaBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);
        $container->addCompilerPass(new RegisterFilePluginsPass());
    }
}
