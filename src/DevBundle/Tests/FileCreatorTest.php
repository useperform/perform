<?php

namespace Perform\DevBundle\Tests;

use Symfony\Component\Filesystem\Filesystem;
use Perform\DevBundle\File\FileCreator;
use Perform\DevBundle\Exception\FileException;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;

/**
 * FileCreatorTest
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class FileCreatorTest extends \PHPUnit_Framework_TestCase
{
    protected $fs;
    protected $twig;
    protected $creator;

    public function setUp()
    {
        $this->fs = $this->getMock(Filesystem::class);
        $this->twig = $this->getMock(\Twig_Environment::class);
        $this->creator = new FileCreator($this->fs, $this->twig);
    }

    public function testForceCreate()
    {
        $this->twig->expects($this->once())
            ->method('render')
            ->with('PerformDevBundle:skeletons:template.twig')
            ->will($this->returnValue('rendered'));
        $this->fs->expects($this->once())
            ->method('dumpFile')
            ->with('/path/to/file.txt', 'rendered');

        $this->creator->create('/path/to/file.txt', 'template.twig');
    }

    public function testCreate()
    {
        $this->twig->expects($this->once())
            ->method('render')
            ->with('PerformDevBundle:skeletons:template.twig')
            ->will($this->returnValue('rendered'));
        $this->fs->expects($this->once())
            ->method('dumpFile')
            ->with('/path/to/file.txt', 'rendered');

        $this->creator->create('/path/to/file.txt', 'template.twig');
    }

    public function testCreateThrowsExceptionWhenFileExists()
    {
        $this->fs->expects($this->any())
            ->method('exists')
            ->with('/path/to/file.txt')
            ->will($this->returnValue(true));
        $this->setExpectedException(FileException::class);

        $this->creator->create('/path/to/file.txt', 'template.twig');
    }

    public function resolveBundleProvider()
    {
        return [
            ['Service\\SomeService'],
            ['\\Service\\SomeService'],
            ['Service\\SomeService\\'],
            ['\\Service\\SomeService\\'],
        ];
    }

    /**
     * @dataProvider resolveBundleProvider
     */
    public function testResolveBundleClass($relativeClass)
    {
        $bundle = $this->getMock(BundleInterface::class);
        $bundle->expects($this->any())
            ->method('getPath')
            ->will($this->returnValue('/bundle/root'));
        $bundle->expects($this->any())
            ->method('getNamespace')
            ->will($this->returnValue('Foo\\BarBundle'));

        $expected = ['/bundle/root/Service/SomeService.php', [
            'foo' => 'bar',
            'classname' => 'SomeService',
            'namespace' => 'Foo\\BarBundle\\Service',
        ]];
        $this->assertSame($expected, $this->creator->resolveBundleClass($bundle, $relativeClass, ['foo' => 'bar']));
    }
}
