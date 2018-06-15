<?php

namespace Perform\BaseBundle\Tests\Fixtures\OptionalEntitiesBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\Extension\Extension;
use Perform\BaseBundle\DependencyInjection\Doctrine;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Perform\BaseBundle\Tests\Fixtures\OptionalEntitiesBundle\Entity\ThreeOptional;
use Perform\BaseBundle\Tests\Fixtures\OptionalEntitiesBundle\Entity\FourOptional;

/**
 * An example of typical usage in a bundle; enable doctrine entities
 * according to configuration values.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class OptionalEntitiesExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        if ($config['three']) {
            Doctrine::addExtraMapping($container, ThreeOptional::class, __DIR__.'/../Resources/config/doctrine_extra/Three.orm.yml');
        }
        if ($config['four']) {
            Doctrine::addExtraMapping($container, FourOptional::class, __DIR__.'/../Resources/config/doctrine_extra/Four.orm.xml');
        }
    }
}
