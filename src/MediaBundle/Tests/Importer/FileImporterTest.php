<?php

namespace Perform\MediaBundle\Tests\Importer;

use League\Flysystem\FilesystemInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Perform\MediaBundle\Importer\FileImporter;
use VirtualFileSystem\FileSystem;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class FileImporterTest extends \PHPUnit_Framework_TestCase
{
    protected $flysystem;
    protected $platform;
    protected $em;
    protected $conn;
    protected $dispatcher;
    protected $importer;

    public function setUp()
    {
        $this->flysystem = $this->getMock(FilesystemInterface::class);
        $this->em = $this->getMock(EntityManagerInterface::class);
        $this->conn = DriverManager::getConnection([
            'url' => 'sqlite:///:memory:',
        ]);
        $this->em->expects($this->any())
            ->method('getConnection')
            ->will($this->returnValue($this->conn));
        $this->dispatcher = $this->getMock(EventDispatcherInterface::class);
        $this->importer = new FileImporter($this->flysystem, $this->em, $this->dispatcher);
        $this->vfs = new FileSystem();
    }

    public function testImportFileSuccessfulMimeGuess()
    {
        $file = $this->importer->import(__FILE__);
        $this->assertSame(36, strlen($file->getId()));
        $this->assertSame('text/x-php', $file->getMimeType());
        $this->assertSame('us-ascii', $file->getCharset());
    }

    public function testImportFileFailedMimeGuess()
    {
        $this->vfs->createFile('/file.txt', 'Hello world');
        $file = $this->importer->import($this->vfs->path('/file.txt'));
        $this->assertSame(36, strlen($file->getId()));
        $this->assertSame('text/plain', $file->getMimeType());
        $this->assertSame('us-ascii', $file->getCharset());
    }

    public function testImportFileSuccessfulMimeGuessNoExtension()
    {
        $file = $this->importer->import(__DIR__.'/../fixtures/binary_no_extension');
        $this->assertSame(36, strlen($file->getId()));
        $this->assertSame('application/octet-stream', $file->getMimeType());
        $this->assertSame('binary', $file->getCharset());
        $this->assertSame('.bin', substr($file->getFilename(), -4));
    }
}
