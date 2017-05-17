<?php

namespace Perform\DevBundle\BundleResource;

use Perform\ContactBundle\PerformContactBundle;

/**
 * ContactBundleResource
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class ContactBundleResource implements ParentResourceInterface
{
    public function getBundleName()
    {
        return 'PerformContactBundle';
    }

    public function getBundleClass()
    {
        return PerformContactBundle::class;
    }

    public function getRoutes()
    {
        return '
perform_contact:
    resource: "@PerformContactBundle/Resources/config/routing.yml"
    prefix: /admin/contact
';
    }

    public function getConfig()
    {
    }

    public function getRequiredBundles()
    {
        return [];
    }

    public function getOptionalBundles()
    {
        return [];
    }

    public function getComposerPackage()
    {
        return 'perform/contact-bundle';
    }

    public function getOptionalComposerPackages()
    {
        return [];
    }
}
