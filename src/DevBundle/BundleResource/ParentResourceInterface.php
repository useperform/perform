<?php

namespace Perform\DevBundle\BundleResource;

/**
 * ParentResourceInterface instances are available to add to an
 * applicaiton using AddBundleCommand.
 *
 * They define the other bundles they may need, as well
 * as suggesting optional bundles and composer packages.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
interface ParentResourceInterface extends ResourceInterface
{
    public function getRequiredBundles();

    public function getComposerPackage();
}
