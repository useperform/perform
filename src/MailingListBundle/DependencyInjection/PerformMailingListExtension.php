<?php

namespace Perform\MailingListBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;
use Perform\MailingListBundle\Form\Type\EmailOnlyType;
use Perform\MailingListBundle\Form\Type\EmailAndNameType;

/**
 * PerformMailingListExtension.
 **/
class PerformMailingListExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');

        $factory = $container->getDefinition('perform_mailing_list.form_factory');
        $factory->addMethodCall('addType', ['email_only', EmailOnlyType::class]);
        $factory->addMethodCall('addType', ['email_name', EmailAndNameType::class]);
    }
}
