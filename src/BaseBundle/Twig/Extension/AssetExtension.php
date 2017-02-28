<?php

namespace Perform\BaseBundle\Twig\Extension;

use Perform\BaseBundle\Asset\AssetContainer;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class AssetExtension extends \Twig_Extension
{
    protected $assets;

    public function __construct(AssetContainer $assets)
    {
        $this->assets = $assets;
    }

    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('perform_asset_js', [$this->assets, 'getJs']),
            new \Twig_SimpleFunction('perform_asset_css', [$this->assets, 'getCss']),
        ];
    }

    public function getName()
    {
        return 'perform_base_asset';
    }
}
