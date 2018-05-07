<?php

namespace Perform\BaseBundle\Event;

use Symfony\Component\EventDispatcher\Event;
use Perform\BaseBundle\Asset\Dumper\TargetInterface;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class AssetDumpEvent extends Event
{
    const ADD = 'perform_base.asset_dump.add';
    const REMOVE = 'perform_base.asset_dump.remove';

    protected $targets = [];

    public function addTarget(TargetInterface $target)
    {
        $this->targets[] = $target;
    }

    public function getTargets()
    {
        return $this->targets;
    }
}
