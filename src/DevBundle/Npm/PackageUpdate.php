<?php

namespace Perform\DevBundle\Npm;

final class PackageUpdate
{
    private $existingVersion;
    private $newVersion;
    private $source;

    public function __construct(string $existingVersion, string $newVersion, string $source)
    {
        $this->existingVersion = $existingVersion;
        $this->newVersion = $newVersion;
        $this->source = $source;
    }

    public function isNew(): bool
    {
        return !$this->existingVersion;
    }

    public function getExistingVersion(): string
    {
        return $this->existingVersion;
    }

    public function getNewVersion(): string
    {
        return $this->newVersion;;
    }

    public function getSource(): string
    {
        return $this->source;
    }
}
