<?php

namespace Perform\BaseBundle\Doctrine;

use Doctrine\Common\Persistence\Mapping\Driver\FileLocator;
use Doctrine\Common\Persistence\Mapping\MappingException;

/**
 * A file locator that takes an array of file names and does not search directories.
 *
 * Used for loading optional doctrine entities.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class MappingFileLocator implements FileLocator
{
    protected $files;

    public function __construct(array $files = [])
    {
        $this->files = $files;
    }

    public function findMappingFile($className) {
        if (!isset($this->files[$className])) {
            throw MappingException::classNotFoundInNamespaces($className, $className);
        }
        if (!file_exists($this->files[$className])) {
            throw MappingException::mappingFileNotFound($className, $this->files[$className]);
        }

        return $this->files[$className];
    }

    public function getAllClassNames($globalBasename) {
        return array_keys($this->files);
    }

    public function fileExists($className) {
        return isset($this->files[$className]) && file_exists($this->files[$className]);
    }

    public function getPaths() {
        return array_values($this->files);
    }

    public function getFileExtension() {
        return '';
    }
}
