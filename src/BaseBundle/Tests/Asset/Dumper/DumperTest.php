<?php

namespace Perform\BaseBundle\Tests\Asset\Dumper;

use PHPUnit\Framework\TestCase;
use Perform\BaseBundle\Asset\Dumper\Dumper;
use Perform\BaseBundle\Asset\Dumper\TargetInterface;
use Symfony\Component\Filesystem\Filesystem;
use VirtualFileSystem\FileSystem as VirtualFileSystem;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class DumperTest extends TestCase
{
    private $vfs;
    private $fs;
    private $dumper;

    public function setUp()
    {
        $this->vfs = new VirtualFileSystem();
        $this->fs = $this->createMock(Filesystem::class);
        $this->dumper = new Dumper($this->fs);
    }

    public function testAssetIsSaved()
    {
        $target = $this->target($this->vfs->path('/info.txt'), 'Test file');
        $this->fs->expects($this->once())
            ->method('dumpFile')
            ->with($this->vfs->path('/info.txt'), 'Test file')
            ->will($this->returnCallback(function($path, $contents) {
                file_put_contents($path, $contents);
            }));

        $this->dumper->dump($target);
        $this->assertSame('Test file', @file_get_contents($this->vfs->path('/info.txt')));
    }

    public function testSameContentsAreNotDumpedTwice()
    {
        $target = $this->target($this->vfs->path('/info.txt'), 'Test file');
        $this->fs->expects($this->once())
            ->method('dumpFile')
            ->with($this->vfs->path('/info.txt'), 'Test file')
            ->will($this->returnCallback(function($path, $contents) {
                file_put_contents($path, $contents);
            }));

        $this->dumper->dump($target);
        $this->dumper->dump($target);
        $this->dumper->dump($target);
        $this->dumper->dump($target);
        $this->assertSame('Test file', @file_get_contents($this->vfs->path('/info.txt')));
    }

    public function testContentsCanBeChanged()
    {
        $this->fs->expects($this->exactly(2))
            ->method('dumpFile')
            ->with($this->vfs->path('/info.txt'))
            ->will($this->returnCallback(function($path, $contents) {
                file_put_contents($path, $contents);
            }));

        ;
        $this->dumper->dump($this->target($this->vfs->path('/info.txt'), 'One'));
        $this->assertSame('One', @file_get_contents($this->vfs->path('/info.txt')));

        $this->dumper->dump($this->target($this->vfs->path('/info.txt'), 'Two'));
        $this->assertSame('Two', @file_get_contents($this->vfs->path('/info.txt')));
    }

    private function target($filename, $contents)
    {
        $target = $this->createMock(TargetInterface::class);
        $target->expects($this->any())
            ->method('getFilename')
            ->will($this->returnValue($filename));
        $target->expects($this->any())
            ->method('getContents')
            ->will($this->returnValue($contents));

        return $target;
    }
}
