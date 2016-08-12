<?php

namespace Admin\CmsBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Admin\CmsBundle\DependencyInjection\Compiler\RegisterBlockTypesPass;

/**
 * AdminCmsBundle
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class AdminCmsBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);
        $container->addCompilerPass(new RegisterBlockTypesPass());
    }
}
