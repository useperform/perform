<?php

namespace Perform\ContactBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Perform\ContactBundle\DependencyInjection\Compiler\FormTemplatePass;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * PerformContactBundle.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class PerformContactBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);
        $container->addCompilerPass(new FormTemplatePass());
    }
}
