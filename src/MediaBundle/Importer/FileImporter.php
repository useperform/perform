<?php

namespace Perform\MediaBundle\Importer;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Doctrine\ORM\EntityManagerInterface;
use Perform\MediaBundle\Entity\File;
use Perform\UserBundle\Entity\User;
use Perform\MediaBundle\Event\FileEvent;
use Symfony\Component\Finder\Finder;
use Perform\MediaBundle\Bucket\BucketRegistryInterface;
use Perform\MediaBundle\Entity\Location;
use Perform\MediaBundle\Exception\InvalidFileSizeException;
use Perform\MediaBundle\Bucket\BucketInterface;
use Perform\MediaBundle\MediaResource;
use Perform\MediaBundle\File\FinfoParser;

/**
 * Add files to the media library.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class FileImporter
{
    protected $bucketRegistry;
    protected $entityManager;
    protected $dispatcher;
    protected $parser;

    public function __construct(BucketRegistryInterface $bucketRegistry, EntityManagerInterface $entityManager, EventDispatcherInterface $dispatcher)
    {
        $this->bucketRegistry = $bucketRegistry;
        $this->entityManager = $entityManager;
        $this->dispatcher = $dispatcher;
        $this->parser = new FinfoParser();
    }

    /**
     * Import a new resource into the media library.
     *
     * @param MediaResource $resource
     * @param string|null   $bucketName The name of the bucket to store the imported file
     */
    public function import(MediaResource $resource, $bucketName = null)
    {
        $bucket = $bucketName ?
                $this->bucketRegistry->get($bucketName) :
                $this->bucketRegistry->getDefault();
        try {
            $this->entityManager->beginTransaction();
            $file = $this->createAndSaveEntity($resource, $bucket);
            if ($resource->isFile()) {
                $bucket->save($file->getLocation(), fopen($resource->getPath(), 'r'));
            }
            $this->entityManager->commit();
        } catch (\Exception $e) {
            $this->entityManager->rollback();
            throw $e;
        }

        // run this in the background
        $this->process($file, $resource);

        return $file;
    }

    protected function createAndSaveEntity(MediaResource $resource, BucketInterface $bucket)
    {
        $file = File::fromResource($resource);
        $file->setStatus(File::STATUS_NEW);
        $file->setBucketName($bucket->getName());

        // set guid manually so a location can be created before saving to the database
        $file->setId($this->generateUuid());

        if ($resource->isFile()) {
            $pathname = $resource->getPath();
            $this->validateFileSize($bucket, $pathname);

            list($mimeType, $charset, $extension) = $this->parser->parse($pathname);
            $file->setMimeType($mimeType);
            $file->setCharset($charset);
            $file->setLocation(Location::file(sprintf('%s.%s', sha1($file->getId()), $extension)));
        } else {
            $file->setMimeType('');
            $file->setCharset('');
            $file->setLocation(Location::url($resource->getPath()));
        }

        $file->setType($this->findType($file, $resource));
        $this->dispatcher->dispatch(FileEvent::CREATE, new FileEvent($file));
        $this->entityManager->persist($file);
        $this->entityManager->flush();

        return $file;
    }

    /**
     * Import a file into the media library.
     *
     * @param string      $pathname   The location of the file or directory
     * @param string|null $name       Optionally, the name to give the media
     * @param User|null   $owner      The optional owner of the files
     * @param string|null $bucketName The name of the bucket to store the imported files
     */
    public function importFile($pathname, $name = null, User $owner = null, $bucketName = null)
    {
        return $this->import(new MediaResource($pathname, $name, $owner), $bucketName);
    }

    /**
     * Import a directory of files into the media library.
     *
     * @param string      $pathname   The location of the directory
     * @param User|null   $owner      The optional owner of the files
     * @param string|null $bucketName The name of the bucket to store the imported files
     * @param array       $extensions Only import the files with the given extensions
     */
    public function importDirectory($pathname, User $owner = null, $bucketName = null, array $extensions = [])
    {
        $finder = Finder::create()
                ->files()
                ->in($pathname);
        foreach ($extensions as $ext) {
            $finder->name(sprintf('/\\.%s$/i', trim($ext, '.')));
        }
        $files = [];

        foreach ($finder as $file) {
            $files[] = $this->import(new MediaResource($file->getPathname(), null, $owner), $bucketName);
        }

        return $files;
    }

    /**
     * Import the URL of a file into the media library.
     *
     * @param string      $url        The URL of the file
     * @param string|null $name       The name to give the file. If null, use the filename.
     * @param User|null   $owner      The optional owner of the file
     * @param string|null $bucketName The name of the bucket to store the imported files
     */
    public function importUrl($url, $name = null, User $owner = null, $bucketName = null)
    {
        $local = tempnam(sys_get_temp_dir(), 'perform-media');
        copy($url, $local);
        if (!$name) {
            $name = basename(parse_url($url, PHP_URL_PATH));
        }
        $this->import(new MediaResource($local, $name, $owner), $bucketName);
        unlink($local);
    }

    public function process(File $file, MediaResource $resource)
    {
        $bucket = $this->bucketRegistry->getForFile($file);
        try {
            $bucket->getMediaType($file->getType())->process($file, $resource, $bucket);
            $this->dispatcher->dispatch(FileEvent::PROCESS, new FileEvent($file));

            $file->setStatus(FILE::STATUS_OK);
            $this->entityManager->persist($file);
            $this->entityManager->flush();
            $resource->delete();
        } catch (\Exception $e) {
            $file->setStatus(FILE::STATUS_ERROR);
            $this->entityManager->persist($file);
            $this->entityManager->flush();
            throw $e;
        }
    }

    /**
     * Fetch the file from storage and reprocess it again.
     */
    public function reprocess(File $file)
    {
        $location = $file->getLocation();
        $bucket = $this->bucketRegistry->getForFile($file);
        if ($location->isFile()) {
            $downloadedFile = tempnam(sys_get_temp_dir(), 'perform-media');
            stream_copy_to_stream($bucket->read($location), fopen($downloadedFile, 'r+'));
        }

        $resource = new MediaResource(
            $location->isFile() ? $downloadedFile : $location->getPath(),
            $file->getName(),
            $file->getOwner()
        );
        $resource->deleteAfterProcess();

        foreach ($file->getExtraLocations() as $location) {
            $bucket->delete($location);
            $this->entityManager->remove($location);
        }
        $this->entityManager->flush();

        return $this->process($file, $resource);
    }

    /**
     * Remove a file from the database and delete it from its bucket.
     *
     * @param File $file
     */
    public function delete(File $file)
    {
        $bucket = $this->bucketRegistry->getForFile($file);
        $this->entityManager->beginTransaction();
        try {
            $this->entityManager->remove($file);
            $this->entityManager->flush();
            $this->dispatcher->dispatch(FileEvent::DELETE, new FileEvent($file));
            $bucket->deleteFile($file);
            $this->entityManager->commit();
        } catch (\Exception $e) {
            $this->entityManager->rollback();
            throw $e;
        }
    }

    /**
     * Get the canonical URL of a media item.
     * The URL may not be public.
     *
     * @param File $file
     *
     * @return string
     */
    public function getUrl(File $file)
    {
        $bucket = $this->bucketRegistry->getForFile($file);

        return $bucket->getUrlGenerator()->generate($file->getLocation());
    }

    public function getSuitableUrl(File $file, array $criteria = [])
    {
        $bucket = $this->bucketRegistry->getForFile($file);
        $type = $bucket->getMediaType($file->getType());
        $location = $type->getSuitableLocation($file, $criteria);

        return $bucket->getUrlGenerator()->generate($location);
    }

    /**
     * Can't use UuidGenerator, since it depends on EntityManager, not EntityManagerInterface.
     *
     * The UuidGenerator can replace this method when upgrading to doctrine 3.
     *
     * See https://github.com/doctrine/doctrine2/pull/6599
     */
    protected function generateUuid()
    {
        $connection = $this->entityManager->getConnection();
        $sql = 'SELECT '.$connection->getDatabasePlatform()->getGuidExpression();

        return $connection->query($sql)->fetchColumn(0);
    }

    protected function validateFileSize(BucketInterface $bucket, $pathname)
    {
        $filesize = filesize($pathname);
        if ($filesize < $bucket->getMinSize() || $filesize > $bucket->getMaxSize()) {
            throw new InvalidFileSizeException(sprintf(
                'Files added to the "%s" bucket must be between %s and %s bytes, the supplied file is %s bytes.',
                $bucket->getName(),
                $bucket->getMinSize(),
                $bucket->getMaxSize(),
                $filesize));
        }
    }

    protected function findType(File $file, MediaResource $resource)
    {
        $bucket = $this->bucketRegistry->getForFile($file);
        foreach ($bucket->getMediaTypes() as $name => $type) {
            if ($type->supports($file, $resource)) {
                return $name;
            }
        }
    }
}
