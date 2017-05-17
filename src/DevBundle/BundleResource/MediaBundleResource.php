<?php

namespace Perform\DevBundle\BundleResource;

use Perform\MediaBundle\PerformMediaBundle;

/**
 * MediaBundleResource
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class MediaBundleResource implements ParentResourceInterface
{
    public function getBundleName()
    {
        return 'PerformMediaBundle';
    }

    public function getBundleClass()
    {
        return PerformMediaBundle::class;
    }

    public function getRoutes()
    {
        return '
perform_media:
    resource: "@PerformMediaBundle/Resources/config/routing.yml"
    prefix: /admin/media
';
    }

    public function getConfig(array $includedResources = [], array $includedComposerPackages = [])
    {
        return '
perform_media:
    plugins:
        - image
        - pdf
        - audio
        - other
';
    }

    public function getRequiredBundles()
    {
        return [
            'OneupFlysystemBundle',
        ];
    }

    public function getOptionalBundles()
    {
        return [];
    }

    public function getComposerPackage()
    {
        return 'perform/media-bundle';
    }

    public function getOptionalComposerPackages()
    {
        return [
            'imagine/imagine' => 'To use the image plugin',
        ];
    }
}
