<?php

namespace Perform\RichContentBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;
use Perform\RichContentBundle\DataFixtures\Profile\ProfileInterface;
use Perform\BaseBundle\DependencyInjection\Assets;
use Perform\BaseBundle\DependencyInjection\Compiler\FormTemplatesPass;
use Perform\DevBundle\PerformDevBundle;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class PerformRichContentExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $container->setParameter('perform_rich_content.block_types', $config['block_types']);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');
        if (\class_exists(PerformDevBundle::class)) {
            $loader->load('services/dev.yml');
        }

        $container->registerForAutoconfiguration(ProfileInterface::class)->addTag('perform_rich_content.fixture_profile');
        Assets::addNamespace($container, 'perform-rich-content', __DIR__.'/../Resources');
        Assets::addExtraSass($container, '~perform-rich-content/scss/components.scss');
        Assets::addExtraJavascript($container, 'richContent', __DIR__.'/../Resources/js/module.js');
        FormTemplatesPass::addTemplate($container, '@PerformRichContent/form_types.html.twig');
    }
}
