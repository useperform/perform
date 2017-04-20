<?php

namespace Perform\DevBundle\Tests;

use Symfony\Component\Filesystem\Filesystem;
use Perform\DevBundle\File\FileCreator;
use Perform\DevBundle\Exception\FileException;

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
}
