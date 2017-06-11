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
        $creator->createInBundle($bundle, 'Resources/views/base.html.twig', 'frontend/twbs3/base.html.twig.twig', [
            'bundleName' => $bundle->getName(),
            'bundleAssetName' => strtolower(substr($bundle->getName(), 0, -6)),
        ]);
        $creator->createInBundle($bundle, 'Resources/views/nav.html.twig', 'frontend/twbs3/nav.html.twig.twig');

        $creator->createInBundle($bundle, 'package.json', 'frontend/twbs3/package.json.twig');
        $creator->createInBundle($bundle, 'gulpfile.js', 'frontend/twbs3/gulpfile.js.twig');
        $creator->createInBundle($bundle, 'install_assets.sh', 'frontend/twbs3/install_assets.sh.twig');
        $creator->chmodInBundle($bundle, 'install_assets.sh', 0755);

        $creator->createInBundle($bundle, 'Resources/scss/app.scss', 'frontend/twbs3/app.scss.twig');
        $creator->createInBundle($bundle, 'Resources/scss/vendors.scss', 'frontend/twbs3/vendors.scss.twig');
        $creator->createInBundle($bundle, 'Resources/scss/variables.scss', 'frontend/twbs3/variables.scss.twig');
    }

    public function createPage(BundleInterface $bundle, FileCreator $creator, $page)
    {
        $pieces = explode('/', $page);
        $creator->createInBundle($bundle, 'Resources/views/'.$page.'.html.twig', 'frontend/twbs3/page.html.twig.twig', [
            'bundleName' => $bundle->getName(),
            'title' => ucfirst(array_pop($pieces)),
        ]);
    }
}
