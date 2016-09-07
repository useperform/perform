<?php

namespace Admin\CmsBundle\CacheWarmer;

use Symfony\Component\HttpKernel\CacheWarmer\CacheWarmerInterface;
use Admin\CmsBundle\Block\BlockTypeRegistry;

/**
 * GulpSourcesWarmer
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class GulpSourcesWarmer implements CacheWarmerInterface
{
    protected $registry;

    public function __construct(BlockTypeRegistry $registry)
    {
        $this->registry = $registry;
    }

    public function warmUp($cacheDir)
    {
        $file = __DIR__.'/../gulp-sources.js';
        $sources = ['Resources/js/blocks.js'];

        foreach ($this->registry->getTypes() as $name => $type) {
            $sources[] = 'Resources/js/'.$name.'.block.js';
        }

        $sources[] = 'Resources/js/app.js';

        $content = 'module.exports = ['
                 .PHP_EOL
                 .'"'
                 .implode('",'.PHP_EOL.'"', $sources).'"'
                 .PHP_EOL.
                 '];'
                 ;
        file_put_contents($file, $content);
    }

    public function isOptional()
    {
        return false;
    }
}
