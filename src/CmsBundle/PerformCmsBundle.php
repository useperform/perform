<?php

namespace Perform\CmsBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Perform\CmsBundle\DependencyInjection\Compiler\RegisterBlockTypesPass;

/**
 * PerformCmsBundle
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class PerformCmsBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);
        $container->addCompilerPass(new RegisterBlockTypesPass());
    }
}
