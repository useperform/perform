<?php

namespace Perform\MediaBundle\Tests\Importer;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Perform\MediaBundle\Importer\FileImporter;
use VirtualFileSystem\FileSystem;
use Doctrine\DBAL\DriverManager;
use Perform\MediaBundle\Entity\File;
use Perform\MediaBundle\Event\FileEvent;
use Perform\UserBundle\Entity\User;
use Perform\MediaBundle\Bucket\BucketInterface;
use Perform\MediaBundle\Bucket\BucketRegistryInterface;
use Perform\MediaBundle\Entity\Location;
use Perform\MediaBundle\MediaType\MediaTypeRegistry;
use Perform\MediaBundle\MediaType\MediaTypeInterface;

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
    protected $vfs;

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
        $this->mediaTypeRegistry = $this->getMockBuilder(MediaTypeRegistry::class)
                                 ->disableOriginalConstructor()
                                 ->getMock();
        $this->mediaTypeRegistry->expects($this->any())
            ->method('get')
            ->will($this->returnValue($this->getMock(MediaTypeInterface::class)));
        $this->importer = new FileImporter($this->bucketRegistry, $this->em, $this->mediaTypeRegistry, $this->dispatcher);
        $this->vfs = new FileSystem();
    }

    private function expectDefaultBucket()
    {
        $bucket = $this->mockBucket('_default');
        $this->bucketRegistry->expects($this->any())
            ->method('getDefault')
            ->will($this->returnValue($bucket));
        $this->bucketRegistry->expects($this->any())
            ->method('getForFile')
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
        $this->bucketRegistry->expects($this->any())
            ->method('getForFile')
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
        $bucket->expects($this->any())
            ->method('getMediaTypes')
            ->will($this->returnValue([]));

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
        $bucket = $this->expectBucket('binaries');

        $this->em->expects($this->once())
            ->method('remove')
            ->with($file);
        $this->em->expects($this->once())
            ->method('flush');
        $bucket->expects($this->once())
            ->method('deleteFile')
            ->with($file);
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
        $files = $this->importer->importDirectory($this->vfs->path('/dir'), $owner, null, []);
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
        $files = $this->importer->importDirectory($this->vfs->path('/dir'), $owner, null, [$extension]);
        $this->assertSame(1, count($files));
        $this->assertSame($owner, $files[0]->getOwner());
        $this->assertSame('file.txt', $files[0]->getName());
    }
}
