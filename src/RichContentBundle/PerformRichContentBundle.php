<?php

namespace Perform\RichContentBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Perform\RichContentBundle\DependencyInjection\Compiler\RegisterBlockTypesPass;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class PerformRichContentBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);
        $container->addCompilerPass(new RegisterBlockTypesPass());
    }
}
