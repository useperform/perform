<?php

namespace Perform\BaseBundle\Event;

use Symfony\Component\EventDispatcher\Event;
use Perform\BaseBundle\Asset\Dumper\PathTarget;
use Perform\BaseBundle\Asset\Dumper\SassTarget;
use Perform\BaseBundle\Asset\Dumper\JavascriptTarget;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class AssetDumpEvent extends Event
{
    const ADD = 'perform_base.asset_dump.add';
    const REMOVE = 'perform_base.asset_dump.remove';

    protected $pathTargets = [];
    protected $sassTargets = [];
    protected $javascriptTargets = [];

    public function addPathTarget(PathTarget $target)
    {
        $this->pathTargets[] = $target;
    }

    public function getPathTargets()
    {
        return $this->pathTargets;
    }

    public function addSassTarget(SassTarget $target)
    {
        $this->sassTargets[] = $target;
    }

    public function getSassTargets()
    {
        return $this->sassTargets;
    }

    public function addJavascriptTarget(JavascriptTarget $target)
    {
        $this->javascriptTargets[] = $target;
    }

    public function getJavascriptTargets()
    {
        return $this->javascriptTargets;
    }
}
