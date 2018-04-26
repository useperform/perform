<?php

namespace Perform\BaseBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Perform\BaseBundle\DependencyInjection\Doctrine;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class DoctrinePass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $defaults = $container->hasParameter(Doctrine::PARAM_RESOLVED_DEFAULTS) ?
                  $container->getParameter(Doctrine::PARAM_RESOLVED_DEFAULTS) : [];
        $config = $container->getParameter(Doctrine::PARAM_RESOLVED_CONFIG);
        $container->getParameterBag()->remove(Doctrine::PARAM_RESOLVED_DEFAULTS);
        $container->getParameterBag()->remove(Doctrine::PARAM_RESOLVED_CONFIG);

        $container->setParameter(Doctrine::PARAM_RESOLVED, array_merge($defaults, $config));
    }
}
