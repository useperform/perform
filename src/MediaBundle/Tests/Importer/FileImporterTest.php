<?php

namespace Perform\MediaBundle\Tests\Importer;

use League\Flysystem\FilesystemInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Perform\MediaBundle\Importer\FileImporter;
use VirtualFileSystem\FileSystem;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Perform\MediaBundle\Entity\File;
use Perform\MediaBundle\Event\FileEvent;
use Perform\UserBundle\Entity\User;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class FileImporterTest extends \PHPUnit_Framework_TestCase
{
    protected $storage;
    protected $platform;
    protected $em;
    protected $conn;
    protected $dispatcher;
    protected $importer;

    public function setUp()
    {
        $this->storage = $this->getMock(FilesystemInterface::class);
        $this->em = $this->getMock(EntityManagerInterface::class);
        $this->conn = DriverManager::getConnection([
            'url' => 'sqlite:///:memory:',
        ]);
        $this->em->expects($this->any())
            ->method('getConnection')
            ->will($this->returnValue($this->conn));
        $this->dispatcher = $this->getMock(EventDispatcherInterface::class);
        $this->importer = new FileImporter($this->storage, $this->em, $this->dispatcher);
        $this->vfs = new FileSystem();
    }

    public function testImportFileSuccessfulMimeGuess()
    {
        $file = $this->importer->importFile(__FILE__);
        $this->assertSame(36, strlen($file->getId()));
        $this->assertSame('text/x-php', $file->getMimeType());
        $this->assertSame('us-ascii', $file->getCharset());
    }

    public function testImportFileFailedMimeGuess()
    {
        $this->vfs->createFile('/file.txt', 'Hello world');
        $file = $this->importer->importFile($this->vfs->path('/file.txt'));
        $this->assertSame(36, strlen($file->getId()));
        $this->assertSame('text/plain', $file->getMimeType());
        $this->assertSame('us-ascii', $file->getCharset());
    }

    public function testImportFileSuccessfulMimeGuessNoExtension()
    {
        $file = $this->importer->importFile(__DIR__.'/../fixtures/binary_no_extension');
        $this->assertSame(36, strlen($file->getId()));
        $this->assertSame('application/octet-stream', $file->getMimeType());
        $this->assertSame('binary', $file->getCharset());
        $this->assertSame('.bin', substr($file->getFilename(), -4));
    }

    public function testDelete()
    {
        $file = new File();
        $file->setFilename('file.bin');
        $this->em->expects($this->once())
            ->method('remove')
            ->with($file);
        $this->em->expects($this->once())
            ->method('flush');
        $this->storage->expects($this->once())
            ->method('delete')
            ->with('file.bin');
        $this->dispatcher->expects($this->once())
            ->method('dispatch')
            ->with(FileEvent::DELETE, new FileEvent($file));

        $this->importer->delete($file);
    }

    public function testImportDirectory()
    {
        $this->vfs->createDirectory('/dir/subdir', true);
        $this->vfs->createFile('/dir/subdir/file.txt', 'Hello world');
        $this->vfs->createFile('/dir/subdir/file2.md', '# Hello world');

        $owner = new User();
        $files = $this->importer->importDirectory($this->vfs->path('/dir'), [], $owner);
        $this->assertSame(2, count($files));
        $this->assertSame($owner, $files[0]->getOwner());
        $this->assertSame('file.txt', $files[0]->getName());
        $this->assertSame($owner, $files[1]->getOwner());
        $this->assertSame('file2.md', $files[1]->getName());
    }

    public function extensionProvider()
    {
        return [
            ['txt'],
            ['.txt'],
            ['TXT'],
            ['.TXT'],
            ['.Txt'],
        ];
    }

    /**
     * @dataProvider extensionProvider
     */
    public function testImportDirectoryFilterExtensions($extension)
    {
        $this->vfs->createDirectory('/dir/subdir', true);
        $this->vfs->createFile('/dir/subdir/file.txt', 'Hello world');
        $this->vfs->createFile('/dir/subdir/file2.md', '# Hello world');

        $owner = new User();
        $files = $this->importer->importDirectory($this->vfs->path('/dir'), [$extension], $owner);
        $this->assertSame(1, count($files));
        $this->assertSame($owner, $files[0]->getOwner());
        $this->assertSame('file.txt', $files[0]->getName());
    }
}
