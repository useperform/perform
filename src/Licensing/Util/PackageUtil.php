<?php

namespace Perform\Licensing\Util;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class PackageUtil
{
    /**
     * Get the versions of the installed perform packages by reading a composer lock file.
     *
     * @return array
     */
    public static function getPerformVersions(array $possibleLockFiles)
    {
        foreach ($possibleLockFiles as $file) {
            if (!file_exists($file)) {
                continue;
            }
            $versions = [];
            $json = json_decode(file_get_contents($file), true);
            if (!isset($json['packages-dev'])) {
                $json['packages-dev'] = [];
            }

            foreach (array_merge($json['packages'], $json['packages-dev']) as $package) {
                if (substr($package['name'], 0, 8) !== 'perform/') {
                    continue;
                }

                if (isset($package['source']['reference'])) {
                    $ref = $package['source']['reference'];
                } elseif (isset($package['dist']['reference'])) {
                    $ref = $package['dist']['reference'];
                } else {
                    $ref = '';
                }
                $versions[$package['name']] = sprintf('%s@%s', $package['version'], $ref);
            }

            return $versions;
        }

        throw new \RuntimeException('Unable to locate a composer lock file to get package versions from.');
    }
}
