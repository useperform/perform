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
use Perform\MediaBundle\Bucket\BucketInterface;
use Perform\MediaBundle\Bucket\BucketRegistryInterface;
use Perform\MediaBundle\Location\Location;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class FileImporterTest extends \PHPUnit_Framework_TestCase
{
    protected $bucketRegistry;
    protected $bucket;
    protected $platform;
    protected $em;
    protected $conn;
    protected $dispatcher;
    protected $importer;

    public function setUp()
    {
        $this->bucketRegistry = $this->getMock(BucketRegistryInterface::class);
        $this->em = $this->getMock(EntityManagerInterface::class);
        $this->conn = DriverManager::getConnection([
            'url' => 'sqlite:///:memory:',
        ]);
        $this->em->expects($this->any())
            ->method('getConnection')
            ->will($this->returnValue($this->conn));
        $this->dispatcher = $this->getMock(EventDispatcherInterface::class);
        $this->importer = new FileImporter($this->bucketRegistry, $this->em, $this->dispatcher);
        $this->vfs = new FileSystem();
    }

    private function expectDefaultBucket()
    {
        $bucket = $this->mockBucket('_default');
        $this->bucketRegistry->expects($this->any())
            ->method('getDefault')
            ->will($this->returnValue($bucket));

        return $bucket;
    }

    private function expectBucket($name)
    {
        $bucket = $this->mockBucket($name);
        $this->bucketRegistry->expects($this->any())
            ->method('get')
            ->with($name)
            ->will($this->returnValue($bucket));

        return $bucket;
    }

    private function expectBucketForFile($name, File $file)
    {
        $bucket = $this->mockBucket($name);
        $this->bucketRegistry->expects($this->any())
            ->method('getForFile')
            ->with($file)
            ->will($this->returnValue($bucket));

        return $bucket;
    }

    private function mockBucket($name)
    {
        $bucket = $this->getMock(BucketInterface::class);
        $bucket->expects($this->any())
            ->method('getName')
            ->will($this->returnValue($name));
        $bucket->expects($this->any())
            ->method('getMinSize')
            ->will($this->returnValue(0));
        $bucket->expects($this->any())
            ->method('getMaxSize')
            ->will($this->returnValue(INF));

        return $bucket;
    }

    public function testImportFileSuccessfulMimeGuess()
    {
        $this->expectDefaultBucket();
        $file = $this->importer->importFile(__FILE__);
        $this->assertSame(36, strlen($file->getId()));
        $this->assertSame('text/x-php', $file->getMimeType());
        $this->assertSame('us-ascii', $file->getCharset());
    }

    public function testImportFileFailedMimeGuess()
    {
        $this->vfs->createFile('/file.txt', 'Hello world');
        $this->expectDefaultBucket();
        $file = $this->importer->importFile($this->vfs->path('/file.txt'));
        $this->assertSame(36, strlen($file->getId()));
        $this->assertSame('text/plain', $file->getMimeType());
        $this->assertSame('us-ascii', $file->getCharset());
    }

    public function testImportFileSuccessfulMimeGuessNoExtension()
    {
        $this->expectDefaultBucket();
        $file = $this->importer->importFile(__DIR__.'/../fixtures/binary_no_extension');
        $this->assertSame(36, strlen($file->getId()));
        $this->assertSame('application/octet-stream', $file->getMimeType());
        $this->assertSame('binary', $file->getCharset());
        $this->assertSame('.bin', substr($file->getLocation()->getPath(), -4));
    }

    public function testDelete()
    {
        $file = new File();
        $location = Location::file('file.bin');
        $file->setLocation($location);
        $bucket = $this->expectBucketForFile('binaries', $file);

        $this->em->expects($this->once())
            ->method('remove')
            ->with($file);
        $this->em->expects($this->once())
            ->method('flush');
        $bucket->expects($this->once())
            ->method('delete')
            ->with($location);
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
        $this->expectDefaultBucket();
        $files = $this->importer->importDirectory($this->vfs->path('/dir'), [], null, $owner);
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
        $this->expectDefaultBucket();
        $files = $this->importer->importDirectory($this->vfs->path('/dir'), [$extension], null, $owner);
        $this->assertSame(1, count($files));
        $this->assertSame($owner, $files[0]->getOwner());
        $this->assertSame('file.txt', $files[0]->getName());
    }
}
