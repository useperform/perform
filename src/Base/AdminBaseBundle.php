<?php

namespace Admin\Base;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Admin\Base\DependencyInjection\Compiler\RegisterAdminsPass;

/**
 * AdminBaseBundle
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class AdminBaseBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);
        $container->addCompilerPass(new RegisterAdminsPass());
    }
}
