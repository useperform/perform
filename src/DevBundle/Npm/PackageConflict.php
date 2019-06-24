<?php

namespace Perform\DevBundle\Npm;

final class PackageConflict
{
    private $existingVersion;
    private $conflictingVersion;
    private $source;

    public function __construct(string $existingVersion, string $conflictingVersion, string $source)
    {
        $this->existingVersion = $existingVersion;
        $this->conflictingVersion = $conflictingVersion;
        $this->source = $source;
    }

    public function getExistingVersion(): string
    {
        return $this->existingVersion;
    }

    public function getConflictingVersion(): string
    {
        return $this->conflictingVersion;;
    }

    public function getSource(): string
    {
        return $this->source;
    }
}
