<?php

namespace Perform\DevBundle\BundleResource;

/**
 * BundleResourceInterface.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
interface BundleResourceInterface
{
    public function getBundleName();

    public function getBundleClass();

    public function getRequiredBundles();

    public function getComposerPackage();

    public function getRoutes();

    public function getConfig();
}
