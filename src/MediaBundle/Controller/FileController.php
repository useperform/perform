<?php

namespace Perform\MediaBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Perform\MediaBundle\Upload\UploadHandler;
use Symfony\Component\HttpFoundation\Request;
use Perform\MediaBundle\Entity\File;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class FileController extends Controller
{
    /**
     * @Route("/find")
     */
    public function findAction(Request $request)
    {
        $files = $this->getDoctrine()->getRepository('PerformMediaBundle:File')->findAll();
        $data = [];
        foreach ($files as $file) {
            $data[] = [
                'id' => $file->getId(),
                'name' => $file->getName(),
                'filename' => $file->getFilename(),
            ];
        }

        return $this->json($data);
    }

    /**
     * @Route("/upload")
     * @Method("POST")
     */
    public function uploadAction(Request $request)
    {
        try {
            $upload = $request->files->get('file');
            if (!$upload instanceof UploadedFile) {
                throw new \Exception('No file submitted.');
            }
            $handler = new UploadHandler();
            $result = $handler->process($request, $upload);

            if ($result->isComplete()) {
                $importer = $this->get('perform_media.importer.file');
                $file = $importer->import($result->getFile()->getPathname(), $result->getClientOriginalName());

                return $this->json(array_merge($result->toArray(), [
                    'id' => $file->getId(),
                    'name' => $file->getName(),
                ]), 200);
            } else {
                // the uploaded file is a chunk of a bigger file
                return $this->json($result->toArray(), 201);
            }
        } catch (\Exception $e) {
            if ($upload instanceof UploadedFile) {
                $name = $upload->getClientOriginalName();
                $context = ['request' => $request->getUri(), 'upload' => $name];
                $msg = sprintf('An error occurred uploading %s.', $name);
            } else {
                $context = ['request' => $request->getUri()];
                $msg = 'An error occurred.';
            }

            $this->get('logger')->error($e->getMessage(), $context);

            return new JsonResponse(['message' => $msg], 500);
        }
    }

    /**
     * @Route("/delete/{id}")
     * @Method("POST")
     */
    public function deleteAction(Request $request, File $file)
    {
        try {
            $this->get('perform_media.importer.file')
                ->delete($file);

            return $this->json([
                'id' => $file->getId(),
                'message' => 'File deleted.',
            ]);
        } catch (\Exception $e) {
            return $this->json([
                'id' => $file->getId(),
                'message' => sprintf(
                    'Unable to delete %s. %s',
                    $file->getName(),
                    $e instanceof ForeignKeyConstraintViolationException ? 'It is being used by another entity.' : 'An error occurred.'),
            ], 500);
        }
    }
}
