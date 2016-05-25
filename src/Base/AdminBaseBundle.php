<?php

namespace Admin\Base;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Admin\Base\DependencyInjection\Compiler\RegisterAdminsPass;
use Admin\Base\DependencyInjection\Compiler\ConfigureMenuPass;
use Admin\Base\DependencyInjection\Compiler\ExtendEntitiesPass;

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
        $container->addCompilerPass(new ConfigureMenuPass());
        $container->addCompilerPass(new ExtendEntitiesPass());
    }
}
