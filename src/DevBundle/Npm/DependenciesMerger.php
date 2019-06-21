<?php

namespace Perform\DevBundle\Npm;

use Composer\Semver\Comparator;
use Composer\Semver\VersionParser;
use Symfony\Component\Filesystem\Exception\FileNotFoundException;

/**
 * Safely merge npm dependencies from many sources into a single package.json.
 **/
final class DependenciesMerger
{
    private $initalDeps = [];
    private $deps = [];
    private $updates = [];
    private $conflicts = [];

    private $parser;

    public function __construct(array $deps)
    {
        $this->deps = $deps;
        $this->initalDeps = $deps;
        $this->parser = new VersionParser();
    }

    public static function createFromPackageFile(string $file): self
    {
        $json = self::parseJsonFile($file);
        $deps = isset($json['dependencies']) ? (array) $json['dependencies'] : [];

        return new self($deps);
    }

    public function getDependencies(): array
    {
        return $this->deps;
    }

    /**
     * @return array<PackageUpdate>
     */
    public function getUpdates(): array
    {
        return $this->updates;
    }

    public function hasUpdates(): bool
    {
        return !empty($this->updates);
    }

    /**
     * @return array<PackageConflict>
     */
    public function getConflicts(): array
    {
        return $this->conflicts;
    }

    public function hasConflicts(): bool
    {
        return !empty($this->conflicts);
    }

    public function mergeDependencies(array $deps, string $source): void
    {
        foreach ($deps as $package => $version) {
            if (!isset($this->deps[$package])) {
                $this->deps[$package] = $version;
                $this->updates[$package] = new PackageUpdate('', $version, $source);
                continue;
            }

            try {
                $existingConstraint = $this->parser->parseConstraints($this->deps[$package]);
                $newConstraint = $this->parser->parseConstraints($version);
            } catch (\Exception $e) {
                $this->conflicts[$package] = new PackageConflict($this->deps[$package], $version, $source);
            }

            // if the two constraints are compatible, use the greater version
            if ($newConstraint->matches($existingConstraint)) {
                if (Comparator::greaterThan($version, $this->deps[$package])) {
                    $this->deps[$package] = $version;
                    $this->updates[$package] = new PackageUpdate($this->initalDeps[$package], $version, $source);
                }
                continue;
            }
            $this->conflicts[$package] = new PackageConflict($this->deps[$package], $version, $source);
        }
    }

    /**
     * Write the updated dependencies to a package.json
     * file. All dependencies in the file will be replaced.
     */
    public function writeToPackageFile(string $file)
    {
        $json = self::parseJsonFile($file);
        $deps = $this->deps;
        ksort($deps);
        $json['dependencies'] = $deps;

        $pretty = json_encode($json, JSON_PRETTY_PRINT);
        file_put_contents($file, str_replace('    ', '  ', $pretty).PHP_EOL);
    }

    private static function parseJsonFile($file)
    {
        if (!file_exists($file)) {
            throw new FileNotFoundException(sprintf('package.json file %s was not found.', $file));
        }
        $json = json_decode(file_get_contents($file), true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception(sprintf('An error ocurred parsing %s: %s', $file, json_last_error_msg()));
        }

        return $json;
    }
}
