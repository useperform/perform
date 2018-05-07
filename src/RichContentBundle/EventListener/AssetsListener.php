<?php

namespace Perform\RichContentBundle\EventListener;

use Perform\BaseBundle\Event\AssetDumpEvent;
use Perform\RichContentBundle\BlockType\BlockTypeRegistry;
use Perform\RichContentBundle\Assets\BlockTypeTarget;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class AssetsListener
{
    protected $registry;
    protected $twig;

    public function __construct(BlockTypeRegistry $registry, \Twig_Environment $twig)
    {
        $this->registry = $registry;
        $this->twig = $twig;
    }

    public function onAddAssets(AssetDumpEvent $event)
    {
        $file = __DIR__.'/../Resources/js/components/blocktypes.js';

        $event->addTarget(new BlockTypeTarget($this->registry, $this->twig, $file));
    }
}
