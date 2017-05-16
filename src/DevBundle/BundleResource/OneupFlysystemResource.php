<?php

namespace Perform\DevBundle\BundleResource;

use Perform\MediaBundle\PerformMediaBundle;
use Oneup\FlysystemBundle\OneupFlysystemBundle;

/**
 * OneupFlysystemResource
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class OneupFlysystemResource implements ResourceInterface
{
    public function getBundleName()
    {
        return 'OneupFlysystemBundle';
    }

    public function getBundleClass()
    {
        return OneupFlysystemBundle::class;
    }

    public function getRoutes()
    {
    }

    public function getConfig(array $includedResources = [], array $includedComposerPackages = [])
    {
        return '
oneup_flysystem:
    adapters:
        main:
            local:
                directory: "%kernel.root_dir%/../web/uploads"
    filesystems:
        main:
            adapter: main
';
    }
}
