<?php

namespace Perform\RichContentBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Perform\RichContentBundle\BlockType\ImageBlockType;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class RegisterFixtureProfilesPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $profiles = [];
        foreach ($container->findTaggedServiceIds('perform_rich_content.fixture_profile') as $service => $tag) {
            $profile = $container->getDefinition($service);
            $class = $profile->getClass();
            $name = $class::getName();

            $profiles[$name] = $profile;
        }

        $container->getDefinition('perform_rich_content.fixture_profile_registry')->setArgument(0, $profiles);
    }
}
