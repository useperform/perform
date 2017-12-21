<?php

namespace Perform\MailingListBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;
use Perform\MailingListBundle\Form\Type\EmailOnlyType;
use Perform\MailingListBundle\Form\Type\EmailAndNameType;
use Perform\MailingListBundle\Connector\LocalConnector;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class PerformMailingListExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = $this->getConfiguration($configs, $container);
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');

        $factory = $container->getDefinition('perform_mailing_list.form_factory');
        $factory->addMethodCall('addType', ['email_only', EmailOnlyType::class]);
        $factory->addMethodCall('addType', ['email_name', EmailAndNameType::class]);

        $connectors = [];
        foreach ($config['connectors'] as $name => $connectorConfig) {
            $connectors[$name] = $this->createConnector($container, $connectorConfig);
        }

        $container->getDefinition('perform_mailing_list.manager')
            ->setArgument(1, $connectors);
    }

    protected function createConnector(ContainerBuilder $container, array $config)
    {
        switch ($config['connector']) {
        case 'local':
            $def = new Definition(LocalConnector::class);
            $def->setArgument(0, new Reference(sprintf('doctrine.orm.%s_entity_manager', $config['entity_manager'] ?: 'default')));

            return $def;
        }

        throw new \Exception(sprintf('Unable to configure unknown mailing list connector type "%s"', $config['connector']));
    }
}
