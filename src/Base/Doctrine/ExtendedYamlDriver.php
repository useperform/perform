<?php

namespace Admin\Base\Doctrine;

use Doctrine\ORM\Mapping\Driver\SimplifiedYamlDriver;
use Symfony\Component\Yaml\Yaml;

/**
 * ExtendedYamlDriver sets bundle entities as mappedSuperclasses if they are
 * extended by the application.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class ExtendedYamlDriver extends SimplifiedYamlDriver
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
