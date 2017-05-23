<?php

namespace Perform\DevBundle\Frontend;

use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Perform\DevBundle\File\FileCreator;

/**
 * Bootstrap3Frontend
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class Bootstrap3Frontend implements FrontendInterface
{
    public function getName()
    {
        return 'twbs3';
    }

    public function createBaseFiles(BundleInterface $bundle, FileCreator $creator)
    {
        //package.json, gulpfile, base.html.twig, nav.html.twig, app.scss
    }
}
