<?php

namespace Admin\MediaBundle\Importer;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Dflydev\ApacheMimeTypes\PhpRepository as MimeTypesRepository;
use League\Flysystem\FilesystemInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Admin\MediaBundle\Entity\File;
use Doctrine\ORM\Id\UuidGenerator;
use Admin\AppBundle\Entity\User;
use Admin\MediaBundle\Event\FileEvent;

/**
 * Add files to the media library.
 **/
class FileImporter
{
    protected $storage;
    protected $entityManager;
    protected $repository;
    protected $dispatcher;

    public function __construct(FilesystemInterface $storage, EntityManagerInterface $entityManager, EventDispatcherInterface $dispatcher)
    {
        $this->storage = $storage;
        $this->entityManager = $entityManager;
        $this->repository = $entityManager->getRepository('AdminMediaBundle:File');
        $this->dispatcher = $dispatcher;
    }

    /**
     * @return FilesystemInterface
     */
    public function getFilesystem()
    {
        return $this->storage;
    }

    /**
     * Get an upload error suitable for displaying to a user.
     *
     * @return string
     */
    public function getUserFacingUploadError(UploadedFile $file)
    {
        static $errors = [
            UPLOAD_ERR_INI_SIZE => 'The file "%s" is too large.',
            UPLOAD_ERR_FORM_SIZE => 'The file "%s" is too large.',
            UPLOAD_ERR_PARTIAL => 'The file "%s" was only partially uploaded.',
            UPLOAD_ERR_NO_FILE => 'No file was uploaded.',
            // UPLOAD_ERR_CANT_WRITE
            // UPLOAD_ERR_NO_TMP_DIR
            // UPLOAD_ERR_EXTENSION
            // all default to error below
        ];

        $errorCode = $file->getError();
        $message = isset($errors[$errorCode]) ? $errors[$errorCode] : 'The file "%s" could not be uploaded';

        return sprintf($message, $file->getClientOriginalName());
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
        $name = $name ?: basename($pathname);
        $file = new File();
        try {
            $connection = $this->entityManager->getConnection();
            $connection->beginTransaction();

            //set guid manually to have it available for hashed filename before insert

            $uuid = new UuidGenerator();
            $file->setId($uuid->generate($this->entityManager, $file));

            $this->setMimeType($file, $pathname);
            $file->setName($name);
            $extension = $this->getExtension($file, $pathname);
            $file->setFilename(sprintf('%s.%s', sha1($file->getId()), $extension));
            if ($owner) {
                $file->setOwner($owner);
            }

            $this->storage->writeStream($file->getFilename(), fopen($pathname, 'r'));
            $this->dispatcher->dispatch(FileEvent::CREATE, new FileEvent($file));
            $this->entityManager->persist($file);
            $this->entityManager->flush();
            $this->dispatcher->dispatch(FileEvent::PROCESS, new FileEvent($file));

            $connection->commit();

            return $file;
        } catch (\Exception $e) {
            if ($file->getFilename() && $this->storage->has($file->getFilename())) {
                $this->storage->delete($file->getFilename());
            }

            $connection->rollback();
            throw $e;
        }
    }

    /**
     * Remove a file from the database, delete it and perform any cleanup operations.
     *
     * @param File $file
     */
    public function delete(File $file)
    {
        $connection = $this->entityManager->getConnection();
        $connection->transactional(function($connection) use ($file) {
            $this->entityManager->remove($file);
            $this->entityManager->flush();

            try {
                $this->storage->delete($file->getFilename());
            } catch (FileNotFoundException $e) {
            }

            $this->dispatcher->dispatch(FileEvent::DELETE, new FileEvent($file));
        });
    }

    protected function setMimeType(File $file, $filename)
    {
        $finfo = new \Finfo(FILEINFO_MIME);
        $info = explode('; charset=', $finfo->file($filename));
        if (count($info) !== 2) {
            throw new \Exception("Could not read mime type of $filename");
        }
        $file->setMimeType($info[0]);
        $file->setCharset($info[1]);
    }

    protected function getExtension(File $file, $filename)
    {
        $suppliedExtension = pathinfo($filename, PATHINFO_EXTENSION);
        if ($suppliedExtension) {
            return $suppliedExtension;
        }

        $mimeType = $file->getMimeType();
        $mimes = new MimeTypesRepository();
        $availableExtensions = $mimes->findExtensions($mimeType);

        if (isset($availableExtensions[0])) {
            return $availableExtensions[0];
        }

        throw new \Exception(sprintf('Unable to parse mime type %s and no extension supplied', $mimeType));
    }
}
