<?php

namespace Admin\Base\DataFixtures\ORM;

use Doctrine\Common\Persistence\Mapping\Driver\MappingDriver;
use Doctrine\Common\Persistence\Mapping\MappingException;
use Doctrine\Common\Persistence\Mapping\ClassMetadata;

/**
 * ExplicitMappingDriver.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class ExplicitMappingDriver implements MappingDriver
{
    protected $driver;
    protected $declaredClasses;

    public function __construct(MappingDriver $driver, array $declaredClasses)
    {
        $this->driver = $driver;
        $this->declaredClasses = $declaredClasses;
    }

    public function loadMetadataForClass($className, ClassMetadata $metadata)
    {
        if (!in_array($className, $this->declaredClasses)) {
            throw new MappingException(sprintf('Class "%s" is not listed in explicit mapping', $className));
        }

        return $this->driver->loadMetadataForClass($className, $metadata);
    }

    public function getAllClassNames()
    {
        return $this->declaredClasses;
    }

    public function isTransient($className)
    {
        return $this->driver->isTransient($className);
    }
}
