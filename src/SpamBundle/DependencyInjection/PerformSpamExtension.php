<?php

namespace Perform\SpamBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;
use Perform\Licensing\Licensing;
use Perform\SpamBundle\Checker\TextCheckerInterface;
use Perform\SpamBundle\Checker\FormCheckerInterface;
use Perform\SpamBundle\Checker\RequestCheckerInterface;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class PerformSpamExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        Licensing::validateProject($container);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');

        $container->registerForAutoconfiguration(TextCheckerInterface::class)
            ->addTag('perform_spam.text_checker');
        $container->registerForAutoconfiguration(FormCheckerInterface::class)
            ->addTag('perform_spam.form_checker');
        $container->registerForAutoconfiguration(RequestCheckerInterface::class)
            ->addTag('perform_spam.request_checker');
    }
}
