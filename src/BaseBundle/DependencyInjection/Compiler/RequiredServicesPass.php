<?php

namespace Perform\BaseBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Remove services if the services they require from the framework and other bundles are not available.
 *
 * This pass should be registered with a high priority before any passes that work with tags.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class RequiredServicesPass implements CompilerPassInterface
{
    protected $services;

    public function __construct(array $services)
    {
        $this->services = $services;
    }

    public function process(ContainerBuilder $container)
    {
        foreach ($this->services as $service => $requirements) {
            foreach ($requirements as $requirement) {
                if (!$container->hasDefinition($requirement)) {
                    $container->log($this, sprintf('Removed service "%s"; the required service "%s" is not available.', $service, $requirement));
                    $container->removeDefinition($service);
                    break;
                }
            }
        }
    }
}
