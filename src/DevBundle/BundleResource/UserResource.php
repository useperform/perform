<?php

namespace Perform\DevBundle\BundleResource;

use Perform\UserBundle\PerformUserBundle;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class UserResource implements ParentResourceInterface
{
    public function getBundleName()
    {
        return 'PerformUserBundle';
    }

    public function getBundleClass()
    {
        return PerformUserBundle::class;
    }

    public function getRoutes()
    {
        return '
perform_user_login:
    resource: "@PerformUserBundle/Resources/config/routing_login.yml"
    prefix: /admin

perform_user_password:
    resource: "@PerformUserBundle/Resources/config/routing_password.yml"

perform_user_admin:
    resource: "@PerformUserBundle/Resources/config/routing_admin.yml"
    prefix: /admin/users
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
        return 'perform/user-bundle';
    }

    public function getOptionalComposerPackages()
    {
        return [];
    }
}
