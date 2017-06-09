<?php

namespace Perform\DevBundle\Frontend;

use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Perform\DevBundle\File\FileCreator;

/**
 * Used to generate resources for a 'frontend' bundle.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
interface FrontendInterface
{
    /**
     * @return string
     */
    public function getName();

    public function createBaseFiles(BundleInterface $bundle, FileCreator $creator);

    public function createPage(BundleInterface $bundle, FileCreator $creator, $page);
}
