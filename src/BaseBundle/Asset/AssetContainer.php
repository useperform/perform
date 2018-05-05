<?php

namespace Perform\BaseBundle\Asset;

/**
 * Store a list of css and js assets over the course of a request.
 *
 * Render these assets later using the assets twig extension.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class AssetContainer
{
    protected $js = [];
    protected $inlineJs;
    protected $css = [];

    public function addJs($src)
    {
        if (!in_array($src, $this->js)) {
            $this->js[] = $src;
        }
    }

    public function getJs()
    {
        return $this->js;
    }

    public function addInlineJs($js)
    {
        $this->inlineJs[] = $js;
    }

    public function getInlineJs()
    {
        return $this->inlineJs;
    }

    public function addCss($src)
    {
        if (!in_array($src, $this->css)) {
            $this->css[] = $src;
        }
    }

    public function getCss()
    {
        return $this->css;
    }
}
