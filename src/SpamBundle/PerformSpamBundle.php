<?php

namespace Perform\SpamBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Perform\SpamBundle\DependencyInjection\Compiler\CheckersPass;
use Perform\SpamBundle\DependencyInjection\Compiler\FormTemplatesPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class PerformSpamBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);
        $container->addCompilerPass(new CheckersPass());
        $container->addCompilerPass(new FormTemplatesPass());
    }
}
