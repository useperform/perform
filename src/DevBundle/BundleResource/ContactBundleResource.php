<?php

namespace Perform\DevBundle\BundleResource;

use Perform\ContactBundle\PerformContactBundle;

/**
 * ContactBundleResource
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class ContactBundleResource implements BundleResourceInterface
{
    public function getBundleName()
    {
        return 'PerformContactBundle';
    }

    public function getBundleClass()
    {
        return PerformContactBundle::class;
    }

    public function getRequiredBundleClasses()
    {
        return [];
    }

    public function getComposerPackage()
    {
        return 'perform/contact-bundle';
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
}
