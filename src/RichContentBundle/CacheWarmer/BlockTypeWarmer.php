<?php

namespace Perform\RichContentBundle\CacheWarmer;

use Symfony\Component\HttpKernel\CacheWarmer\CacheWarmerInterface;
use Perform\RichContentBundle\BlockType\BlockTypeRegistry;

/**
 * Prepare javascript sources for building the configured block types
 * into the editor.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class BlockTypeWarmer implements CacheWarmerInterface
{
    protected $registry;
    protected $twig;

    public function __construct(BlockTypeRegistry $registry, \Twig_Environment $twig)
    {
        $this->registry = $registry;
        $this->twig = $twig;
    }

    public function warmUp($cacheDir)
    {
        $file = __DIR__.'/../Resources/js/components/blocktypes.js';

        $types = $this->registry->all();
        ksort($types);
        file_put_contents($file, $this->twig->render('@PerformRichContent/blocktypes.js.twig', [
            'types' => $types,
        ]));
    }

    public function isOptional()
    {
        return false;
    }
}
