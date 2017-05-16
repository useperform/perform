<?php

namespace Perform\DevBundle\BundleResource;

use Perform\MediaBundle\PerformMediaBundle;

/**
 * MediaBundleResource
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class MediaBundleResource implements BundleResourceInterface
{
    public function getBundleName()
    {
        return 'PerformMediaBundle';
    }

    public function getBundleClass()
    {
        return PerformMediaBundle::class;
    }

    public function getRequiredBundleClasses()
    {
        return [];
    }

    public function getComposerPackage()
    {
        return 'perform/media-bundle';
    }

    public function getRoutes()
    {
        return '
perform_media:
    resource: "@PerformMediaBundle/Resources/config/routing.yml"
    prefix: /admin/media
';
    }
}
