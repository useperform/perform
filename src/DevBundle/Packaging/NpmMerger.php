<?php

namespace Perform\DevBundle\Packaging;

use Composer\Semver\Comparator;
use Composer\Semver\Semver;
use Composer\Semver\VersionParser;
use Symfony\Component\Filesystem\Exception\FileNotFoundException;

/**
 * Utility to merge requirements from multiple package.json files into one.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class NpmMerger
{
    protected $parser;

    public function __construct()
    {
        $this->parser = new VersionParser();
    }

    /**
     * @return NpmMergeResult
     */
    public function mergeRequirements(array $existingRequirements, array $newRequirements)
    {
        $resolved = $existingRequirements;

        foreach ($newRequirements as $package => $version) {
            if (!isset($resolved[$package])) {
                $resolved[$package] = $version;
                continue;
            }

            $existingConstraint = $this->parser->parseConstraints($resolved[$package]);
            $newConstraint = $this->parser->parseConstraints($version);
            if ($newConstraint->matches($existingConstraint)) {
                // if the two constraints are compatible, use the greater version
                $resolved[$package] = Comparator::greaterThan($version, $resolved[$package]) ?
                                    $version : $resolved[$package];
                continue;
            }
        }

        return new NpmMergeResult($resolved, [], []);
    }

    /**
     * Load an array of requirements from the given package.json file.
     *
     * @param string $file
     */
    public function loadRequirements($file)
    {
        $json = $this->parseJsonFile($file);
        return isset($json['dependencies']) ? (array) $json['dependencies'] : [];
    }

    /**
     * Write an array of requirements to the given package.json
     * file. All dependencies will be replaced by the given
     * requirements.
     *
     * @param string $file
     * @param array $requirements
     */
    public function writeRequirements($file, array $requirements)
    {
        $json = $this->parseJsonFile($file);
        ksort($requirements);
        $json['dependencies'] = $requirements;

        $pretty = json_encode($json, JSON_PRETTY_PRINT);
        file_put_contents($file, str_replace('    ', '  ', $pretty));
    }

    private function parseJsonFile($file)
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
