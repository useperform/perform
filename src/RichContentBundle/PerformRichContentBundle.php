<?php

namespace Perform\RichContentBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Perform\RichContentBundle\DependencyInjection\Compiler\RegisterBlockTypesPass;
use Perform\RichContentBundle\DependencyInjection\Compiler\RegisterFixtureProfilesPass;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class PerformRichContentBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);
        $container->addCompilerPass(new RegisterBlockTypesPass());
        $container->addCompilerPass(new RegisterFixtureProfilesPass());
    }
}
