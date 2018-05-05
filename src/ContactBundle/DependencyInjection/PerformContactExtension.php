<?php

namespace Perform\ContactBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;
use Perform\Licensing\Licensing;
use Perform\BaseBundle\DependencyInjection\Compiler\FormTemplatesPass;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class PerformContactExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        Licensing::validateProject($container);
        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');
        FormTemplatesPass::addTemplate($container, '@PerformContact/form_types.html.twig');
    }
}
