<?php

namespace Perform\BaseBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Perform\BaseBundle\DependencyInjection\Compiler\RegisterAdminsPass;
use Perform\BaseBundle\DependencyInjection\Compiler\ConfigureMenuPass;
use Perform\BaseBundle\DependencyInjection\Compiler\ConfigureSettingsPass;
use Perform\BaseBundle\DependencyInjection\Compiler\ConfigureActionsPass;
use Perform\BaseBundle\DependencyInjection\Compiler\ConfigureTypesPass;
use Perform\BaseBundle\DependencyInjection\Compiler\DoctrinePass;
use Perform\BaseBundle\DependencyInjection\Compiler\FormTemplatesPass;

/**
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
        $container->addCompilerPass(new ConfigureActionsPass());
        $container->addCompilerPass(new ConfigureTypesPass());
        $container->addCompilerPass(new DoctrinePass());
        $container->addCompilerPass(new FormTemplatesPass());
    }
}
