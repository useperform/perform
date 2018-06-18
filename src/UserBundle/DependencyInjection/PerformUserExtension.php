<?php

namespace Perform\UserBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;
use Perform\Licensing\Licensing;
use Perform\BaseBundle\DependencyInjection\Doctrine;
use Symfony\Component\Security\Core\User\UserInterface;
use Perform\UserBundle\Entity\User;
use Perform\BaseBundle\DependencyInjection\LoopableServiceLocator;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class PerformUserExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        Licensing::validateProject($container);
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');

        $tokenManager = $container->getDefinition('perform_user.reset_token_manager');
        $tokenManager->addArgument($config['reset_token_expiry']);

        Doctrine::registerDefaultImplementation($container, UserInterface::class, User::class);

        $userListener = $container->getDefinition('perform_user.doctrine.user_listener');
        $userListener->setArgument(0, LoopableServiceLocator::createDefinition([
            'encoder_factory' => new Reference('security.encoder_factory'),
        ]));
    }
}
