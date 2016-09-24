<?php

namespace Perform\BaseBundle\Doctrine;

use Doctrine\ORM\Mapping\Driver\SimplifiedYamlDriver as BaseDriver;
use Symfony\Component\Yaml\Yaml;

/**
 * SimplifiedYamlDriver sets bundle entities as mappedSuperclasses if they are
 * extended by the application.
 *
 * This class must be called Simplified<Format>Driver so it can be interpreted
 * correctly by doctrine-extensions.
 *
 * See getDriver() in
 * vendor/gedmo/doctrine-extensions/lib/Gedmo/Mapping/ExtensionMetadataFactory.php
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class SimplifiedYamlDriver extends BaseDriver
{
    protected $extendedEntities = [];

    public function __construct($prefixes, $fileExtension, array $extendedEntities)
    {
        $this->extendedEntities = $extendedEntities;
        parent::__construct($prefixes, $fileExtension);
    }

    protected function loadMappingFile($file)
    {
        $config = Yaml::parse(file_get_contents($file));

        return $this->processConfig($config);
    }

    public function processConfig(array $config)
    {
        foreach ($this->extendedEntities as $parent => $child) {
            if (isset($config[$parent])) {
                $config[$parent]['type'] = 'mappedSuperclass';
            }
        }

        //replace instances of the parent with the child in relation definitions
        array_walk_recursive($config, function (&$value, $key) {
            if ($key === 'targetEntity' && isset($this->extendedEntities[$value])) {
                $value = $this->extendedEntities[$value];
            }
        });

        return $config;
    }
}
