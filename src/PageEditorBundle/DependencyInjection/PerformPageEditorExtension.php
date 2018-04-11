<?php

namespace Perform\PageEditorBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;
use Perform\Licensing\Licensing;
use Perform\BaseBundle\DependencyInjection\PerformBaseExtension;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class PerformPageEditorExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        Licensing::validateProject($container);
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $container->setParameter('perform_page_editor.fixture_definitions', $config['fixtures']);
        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');

        $container->setParameter('perform_page_editor.toolbar.excluded_urls', $config['toolbar']['excluded_urls']);

        if (class_exists(PerformBaseExtension::class)) {
            PerformBaseExtension::addExtraSass($container, ['PerformPageEditorBundle:toolbar.scss']);
            // define a new entrypoint to use page editor standalone on the frontend
        }
    }
}
