<?php

namespace Perform\MediaBundle\Importer;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use League\Flysystem\FilesystemInterface;
use League\Flysystem\FileNotFoundException;
use Doctrine\ORM\EntityManagerInterface;
use Perform\MediaBundle\Entity\File;
use Perform\UserBundle\Entity\User;
use Perform\MediaBundle\Event\FileEvent;
use Mimey\MimeTypes;
use Symfony\Component\Finder\Finder;

/**
 * Add files to the media library.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class FileImporter
{
    protected $storage;
    protected $entityManager;
    protected $repository;
    protected $dispatcher;
    protected $mimes;

    public function __construct(FilesystemInterface $storage, EntityManagerInterface $entityManager, EventDispatcherInterface $dispatcher)
    {
        $this->storage = $storage;
        $this->entityManager = $entityManager;
        $this->repository = $entityManager->getRepository('PerformMediaBundle:File');
        $this->dispatcher = $dispatcher;
        $this->mimes = new MimeTypes();
    }

    /**
     * Import a file or directory into the media library.
     *
     * @param string $pathname The location of the file or directory
     * @param User   $user     The optional owner of the files
     */
    public function import($pathname, User $owner = null)
    {
        return is_dir($pathname) ? $this->importDirectory($pathname, $owner) : $this->importFile($pathname, $owner);
    }

    /**
     * Import a file into the media library.
     *
     * @param string      $pathname The location of the file
     * @param string|null $name     Optionally, the name to give the file
     * @param User        $user     The optional owner of the file
     */
    public function importFile($pathname, $name = null, User $owner = null)
    {
        if (!file_exists($pathname)) {
            throw new \InvalidArgumentException("$pathname does not exist.");
        }
        $file = new File();
        $file->setName($name ?: basename($pathname));
        try {
            $this->entityManager->beginTransaction();

            // set guid manually to have it available for hashed filename before insert
            $file->setId($this->generateUuid());
            $extension = pathinfo($pathname, PATHINFO_EXTENSION);

            list($mimeType, $charset) = $this->getContentType($pathname, $extension);
            $file->setMimeType($mimeType);
            $file->setCharset($charset);

            $file->setFilename(sprintf('%s.%s', sha1($file->getId()), $this->getSuitableExtension($mimeType, $extension)));
            if ($owner) {
                $file->setOwner($owner);
            }

            $this->dispatcher->dispatch(FileEvent::CREATE, new FileEvent($file));
            $this->storage->writeStream($file->getFilename(), fopen($pathname, 'r'));
            $this->dispatcher->dispatch(FileEvent::PROCESS, new FileEvent($file));
            $this->entityManager->persist($file);
            $this->entityManager->flush();

            $this->entityManager->commit();

            return $file;
        } catch (\Exception $e) {
            if ($file->getFilename() && $this->storage->has($file->getFilename())) {
                $this->storage->delete($file->getFilename());
            }

            $this->entityManager->rollback();
            throw $e;
        }
    }

    /**
     * Import a directory of files into the media library.
     *
     * @param string $pathname   The location of the directory
     * @param array  $extensions Only import the files with the given extensions
     * @param User   $user       The optional owner of the files
     */
    public function importDirectory($pathname, array $extensions = [], User $owner = null)
    {
        $finder = Finder::create()
                ->files()
                ->in($pathname);
        foreach ($extensions as $ext) {
            $finder->name(sprintf('/\\.%s$/i', trim($ext, '.')));
        }
        $files = [];

        foreach ($finder as $file) {
            $files[] = $this->importFile($file->getPathname(), null, $owner);
        }

        return $files;
    }

    /**
     * Import the URL of a file into the media library.
     *
     * @param string      $url  The URL of the file
     * @param string|null $name Optionally, the name to give the file
     * @param User        $user The optional owner of the file
     */
    public function importUrl($url, $name = null, User $owner = null)
    {
        $local = tempnam(sys_get_temp_dir(), 'perform-media');
        copy($url, $local);
        if (!$name) {
            $name = basename(parse_url($url, PHP_URL_PATH));
        }
        $this->importFile($local, $name, $owner);
        unlink($local);
    }

    /**
     * Remove a file from the database, delete it and perform any cleanup operations.
     *
     * @param File $file
     */
    public function delete(File $file)
    {
        $connection = $this->entityManager->getConnection();
        $connection->transactional(function ($connection) use ($file) {
            $this->entityManager->remove($file);
            $this->entityManager->flush();

            try {
                $this->storage->delete($file->getFilename());
            } catch (FileNotFoundException $e) {
            }

            $this->dispatcher->dispatch(FileEvent::DELETE, new FileEvent($file));
        });
    }

    /**
     * Get the mimetype and charset for a file.
     *
     * @param string $filename
     * @param string $extension
     */
    protected function getContentType($filename, $extension)
    {
        $finfo = new \Finfo(FILEINFO_MIME);
        $guess = explode('; charset=', @$finfo->file($filename));

        if (count($guess) === 2) {
            return $guess;
        }

        // best effort detection of mimetype and charset
        $mime = $extension ? $this->mimes->getMimeType($extension) : null;
        // getMimeType can return null, default to application/octet-stream
        if (!$mime) {
            $mime = 'application/octet-stream';
        }

        return [
            $mime,
            $this->defaultCharset($mime),
        ];
    }

    /**
     * Get a suitable extension for a file with the given mime type.
     *
     * @param string $mimeType  The mime type of the supplied file
     * @param string $extension The extension of the supplied file
     */
    public function getSuitableExtension($mimeType, $extension)
    {
        $validExtensions = $this->mimes->getAllExtensions($mimeType);

        if (in_array($extension, $validExtensions) || !isset($validExtensions[0])) {
            return $extension;
        }

        return $validExtensions[0];
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

    protected function defaultCharset($mimeType)
    {
        if (substr($mimeType, 0, 5) === 'text/') {
            return 'us-ascii';
        }

        return 'binary';
    }
}
