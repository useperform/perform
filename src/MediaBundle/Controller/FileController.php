<?php

namespace Admin\MediaBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Admin\MediaBundle\Upload\UploadHandler;
use Symfony\Component\HttpFoundation\Request;

/**
 * FileController
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class FileController extends Controller
{
    /**
     * @Route("/")
     * @Template
     */
    public function listAction()
    {
        $files = $this->getDoctrine()->getRepository('AdminMediaBundle:File')->findAll();

        return [
            'files' => $files,
        ];
    }

    /**
     * @Route("/upload")
     * @Template
     */
    public function uploadAction(Request $request)
    {
        $method = $request->getMethod();
        if ($method === 'GET') {
            return [];
        } else if ($method !== 'POST') {
            return new JsonResponse([
                'message' => "$method upload method not supported."
            ], 405);
        }

        try {
            $handler = new UploadHandler();
            $upload = $request->files->get('files');
            if (!$upload instanceof UploadedFile) {
                throw new \Exception('No file submitted.');
            }

            $handler->process($request, $upload);

            //import all completed files (1 at most in this case)
            $importer = $this->get('admin_media.importer.file');
            foreach ($handler->getCompletedFiles() as $name => $file) {
                $importer->import($file->getPathname(), $name);
            }

            //todo - need to return a plaintext response for opera and
            //IE
            //return the status of uploads for the progress bar. This
            //included completed files but also chunks of files too.
            $response = new \stdClass();
            $response->files = [];
            foreach ($handler->getUploadedFiles() as $name => $file) {
                $i = new \stdClass();
                $i->name = $name;
                $i->size = $file->getSize();
                $response->files[] = $i;
            }
            return new JsonResponse($response);
        } catch (\Exception $e) {
            if (isset($upload)) {
                $name = $upload->getClientOriginalName();
                $context = ['request' => $request->getUri(), 'upload' => $name];
                $msg = 'An error occurred uploading '.$name;
            } else {
                $context = ['request' => $request->getUri()];
                $msg = 'An error occurred';
            }

            $this->get('logger')->warning($e->getMessage(), $context);

            return new JsonResponse(['message' => $msg], 500);
        }
    }
}
