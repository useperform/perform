<?php

namespace Perform\DevBundle\File;

use Symfony\Component\Filesystem\Filesystem;

/**
 * RoutingModifier.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class RoutingModifier
{
    protected $configFile;

    const CONFIGS = [
        'PerformContactBundle' => '
perform_contact:
    resource: "@PerformContactBundle/Resources/config/routing.yml"
    prefix: /admin/contact
',
        'PerformMediaBundle' => '
perform_media:
    resource: "@PerformMediaBundle/Resources/config/routing.yml"
    prefix: /admin/media
',
    ];

    public function __construct($configFile)
    {
        $this->configFile = $configFile;
        $this->fs = new Filesystem();
    }

    public function addConfig($yaml)
    {
        $contents = file_get_contents($this->configFile);
        if (strpos($contents, trim($yaml))) {
            //already added
            return;
        }

        return $this->fs->dumpFile($this->configFile, $contents.$yaml);
    }
}
