<?php

namespace Perform\BaseBundle\Tests\Doctrine;

use Perform\BaseBundle\Util\BundleSearcher;
use Perform\BaseBundle\CacheWarmer\SassWarmer;
use Symfony\Component\Filesystem\Filesystem;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class SassWarmerTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->searcher = $this->getMockBuilder(BundleSearcher::class)
                        ->disableOriginalConstructor()
                        ->getMock();
        $this->fs = $this->getMock(Filesystem::class);
    }

    private function mockFile($path)
    {
        $file = $this->getMockBuilder(\SplFileInfo::class)
              ->disableOriginalConstructor()
              ->getMock();
        $file->expects($this->any())
            ->method('getPathname')
            ->will($this->returnValue($path));

        return $file;
    }

    private function expectThemeSearch($theme, $bundle)
    {
        $this->searcher->expects($this->any())
            ->method('findResourcesAtPath')
            ->withConsecutive(
                [sprintf('scss/themes/%s/theme.scss', $theme), [$bundle]],
                [sprintf('scss/themes/%s/variables.scss', $theme), [$bundle]]
            )
            ->will($this->onConsecutiveCalls(
                new \ArrayIterator(['path/to/theme.scss' => $this->mockFile('path/to/theme.scss')]),
                new \ArrayIterator(['path/to/variables.scss' => $this->mockFile('path/to/variables.scss')])
            ));
    }

    public function testLoadDefaultTheme()
    {
        $warmer = new SassWarmer($this->searcher, $this->fs, 'PerformBaseBundle:default', []);
        $this->expectThemeSearch('default', 'PerformBaseBundle');
        $this->fs->expects($this->any())
            ->method('makePathRelative')
            ->withConsecutive(
                ['path/to/theme.scss', SassWarmer::TARGET_DIR],
                ['path/to/variables.scss', SassWarmer::TARGET_DIR]
            )
            ->will($this->onConsecutiveCalls(
                '/traverse/fs/../../path/to/theme.scss',
                '/traverse/fs/../../path/to/variables.scss'
            ));
        $this->fs->expects($this->exactly(2))
            ->method('dumpFile')
            ->withConsecutive(
                [SassWarmer::TARGET_DIR.'_theme.scss', '@import "/traverse/fs/../../path/to/theme.scss";'.PHP_EOL],
                [SassWarmer::TARGET_DIR.'_theme_variables.scss', '@import "/traverse/fs/../../path/to/variables.scss";'.PHP_EOL]
            );

        $warmer->loadTheme();
    }
}
