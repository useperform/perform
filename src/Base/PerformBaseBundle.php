<?php

namespace Perform\Base;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Perform\Base\DependencyInjection\Compiler\RegisterAdminsPass;
use Perform\Base\DependencyInjection\Compiler\ConfigureMenuPass;
use Perform\Base\DependencyInjection\Compiler\ConfigureSettingsPass;
use Perform\Base\DependencyInjection\Compiler\ExtendEntitiesPass;

/**
 * PerformBaseBundle
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class PerformBaseBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);
        $container->addCompilerPass(new RegisterAdminsPass());
        $container->addCompilerPass(new ConfigureMenuPass());
        $container->addCompilerPass(new ConfigureSettingsPass());
        $container->addCompilerPass(new ExtendEntitiesPass());
    }
}
