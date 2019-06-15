<?php

namespace Perform\BaseBundle\Tests\DependencyInjection;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Perform\BaseBundle\Settings\Manager\WriteableSettingsManagerInterface;

/**
 * Temporary until framework-bundle version 4+ is available, allowing for test.container
 */
class MakePublicServicesPass implements CompilerPassInterface
{
    private $services = [];

    public function __construct(array $services)
    {
        $this->services = $services;
    }

    public function process(ContainerBuilder $container)
    {
        foreach ($this->services as $service) {
            if ($container->hasDefinition($service)) {
                $container->getDefinition($service)->setPublic(true);
            }
            if ($container->hasAlias($service)) {
                $container->getAlias($service)->setPublic(true);
            }
        }
    }
}
