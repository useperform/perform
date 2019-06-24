<?php

namespace Perform\BaseBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Perform\BaseBundle\DependencyInjection\Doctrine;
use Doctrine\Common\Persistence\Mapping\MappingException;
use Doctrine\ORM\Mapping\Driver\XmlDriver;
use Doctrine\ORM\Mapping\Driver\YamlDriver;
use Perform\BaseBundle\Doctrine\MappingFileLocator;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class DoctrinePass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $defaults = $container->hasParameter(Doctrine::PARAM_RESOLVED_DEFAULTS) ?
                  $container->getParameter(Doctrine::PARAM_RESOLVED_DEFAULTS) : [];
        $config = $container->getParameter(Doctrine::PARAM_RESOLVED_CONFIG);
        $container->getParameterBag()->remove(Doctrine::PARAM_RESOLVED_DEFAULTS);
        $container->getParameterBag()->remove(Doctrine::PARAM_RESOLVED_CONFIG);

        $container->setParameter(Doctrine::PARAM_RESOLVED, array_merge($defaults, $config));

        $extraMappings = $container->hasParameter(Doctrine::PARAM_EXTRA_MAPPINGS) ?
                       $container->getParameter(Doctrine::PARAM_EXTRA_MAPPINGS) : [];

        foreach ($extraMappings as $mapping) {
            $this->addExtraMapping($container, $mapping[0], $mapping[1]);
        }
    }

    private function addExtraMapping(ContainerBuilder $container, $entityClass, $mappingFile)
    {
        $extension = pathinfo($mappingFile, PATHINFO_EXTENSION);
        $driver = $this->getDriver($container, $extension);
        $locator = $this->getLocator($container);

        $files = $locator->getArgument(0);
        $files[$entityClass] = $mappingFile;
        $locator->setArgument(0, $files);

        $chainDriverService = 'doctrine.orm.default_metadata_driver';
        if (!$container->hasDefinition($chainDriverService)) {
            return;
        }

        $chainDriver = $container->getDefinition($chainDriverService);
        // the driver needs to go the front of the list, as the chain
        // driver will stop when it finds a matching namespace
        $calls = $chainDriver->getMethodCalls();
        array_unshift($calls, ['addDriver', [$driver, $entityClass]]);
        $chainDriver->setMethodCalls($calls);
    }

    private function getLocator(ContainerBuilder $container)
    {
        if (!$container->has(MappingFileLocator::class)) {
            $locator = $container->register(MappingFileLocator::class, MappingFileLocator::class);
            $locator->setArgument(0, []);
        }

        return $container->getDefinition(MappingFileLocator::class);
    }

    private function getDriver(ContainerBuilder $container, $extension)
    {
        switch (strtolower($extension)) {
        case 'xml':
            $driverClass = XmlDriver::class;
            $service = 'perform_base.doctrine.optional_xml_driver';
            break;
        case 'yml':
        case 'yaml':
            $driverClass = YamlDriver::class;
            $service = 'perform_base.doctrine.optional_yaml_driver';
            break;
        default:
            throw new MappingException('Unsupported file extension for optional doctrine mapping: '.$extension);
        }

        if (!$container->has($service)) {
            $driver = $container->register($service, $driverClass);
            $driver->setArgument(0, $this->getLocator($container));
        }

        return $container->getDefinition($service);
    }
}
