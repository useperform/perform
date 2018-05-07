<?php

namespace Perform\RichContentBundle\Assets;

use Perform\BaseBundle\Asset\Dumper\TargetInterface;
use Perform\RichContentBundle\BlockType\BlockTypeRegistry;

/**
 * For dumping the configured block types to use in the editor.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class BlockTypeTarget implements TargetInterface
{
    protected $registry;
    protected $twig;
    protected $filename;

    public function __construct(BlockTypeRegistry $registry, \Twig_Environment $twig, $filename)
    {
        $this->registry = $registry;
        $this->twig = $twig;
        $this->filename = $filename;
    }

    public function getContents()
    {
        $types = $this->registry->all();
        ksort($types);

        return $this->twig->render('@PerformRichContent/blocktypes.js.twig', [
            'types' => $types,
        ]);
    }

    public function getFilename()
    {
        return $this->filename;
    }
}
