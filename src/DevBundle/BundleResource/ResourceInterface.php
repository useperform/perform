<?php

namespace Perform\DevBundle\BundleResource;

/**
 * ResourceInterface defines the bundle class, routes, and config to
 * use when it is added to an application.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
interface ResourceInterface
{
    public function getBundleName();

    public function getBundleClass();

    public function getRoutes();

    public function getConfig();
}
