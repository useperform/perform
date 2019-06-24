<?php

namespace Perform\BaseBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Perform\BaseBundle\DependencyInjection\Compiler\CrudPass;
use Perform\BaseBundle\DependencyInjection\Compiler\CrudVoterPass;
use Perform\BaseBundle\DependencyInjection\Compiler\SettingsPass;
use Perform\BaseBundle\DependencyInjection\Compiler\ActionsPass;
use Perform\BaseBundle\DependencyInjection\Compiler\InstallersPass;
use Perform\BaseBundle\DependencyInjection\Compiler\FieldTypesPass;
use Perform\BaseBundle\DependencyInjection\Compiler\DoctrinePass;
use Perform\BaseBundle\DependencyInjection\Compiler\FormTemplatesPass;
use Perform\BaseBundle\DependencyInjection\Compiler\RequiredServicesPass;
use Symfony\Component\DependencyInjection\Compiler\PassConfig;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class PerformBaseBundle extends Bundle
{
    private static $serviceRequirements = [
        'perform_base.twig.serializer' => ['serializer'],
        'perform_base.menu_builder' => ['knp_menu.factory'],
        'perform_base.menu_renderer' => ['knp_menu.matcher'],
    ];
    public function build(ContainerBuilder $container)
    {
        parent::build($container);
        // high priority, run before any passes working with tags
        $container->addCompilerPass(new RequiredServicesPass(static::$serviceRequirements), PassConfig::TYPE_BEFORE_OPTIMIZATION, 100);
        $container->addCompilerPass(new CrudPass());
        $container->addCompilerPass(new CrudVoterPass());
        $container->addCompilerPass(new SettingsPass());
        $container->addCompilerPass(new ActionsPass());
        $container->addCompilerPass(new InstallersPass());
        $container->addCompilerPass(new FieldTypesPass());
        $container->addCompilerPass(new DoctrinePass());
        $container->addCompilerPass(new FormTemplatesPass());
    }
}
