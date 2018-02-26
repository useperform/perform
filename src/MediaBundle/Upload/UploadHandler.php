<?php

namespace Perform\MediaBundle\Upload;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\Exception\UploadException;

/**
 * Handles files that have been uploaded in chunks, indicated with the
 * CONTENT_RANGE header.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 */
class UploadHandler
{
    /**
     * Process an uploaded file and return a result referencing the uploaded file.
     *
     * If the upload is complete, return a reference to the file.
     *
     * If the upload is a chunk of a bigger file, concatenate it with
     * other chunks in a temporary file and return a reference the temporary file.
     *
     * If the upload is the final chunk of a bigger file, rename the
     * entire file to the name of the upload and return a reference to
     * it.
     *
     * @param Request
     * @param UploadedFile
     *
     * @return UploadResult
     */
    public function process(Request $request, UploadedFile $file)
    {
        if (!$file->isValid()) {
            throw new UploadException($file->getErrorMessage());
        }

        $result = $this->newResult($request, $file);

        switch ($result->getChunkStatus()) {
        case UploadResult::CHUNK_START:
            // start of a file, create a new chunk file
            $chunkPath = $this->getChunkPath($file);
            $chunkFile = $file->move(dirname($chunkPath), basename($chunkPath));
            $result->setFile($chunkFile);
            break;

        case UploadResult::CHUNK_PARTIAL:
            // middle of a file, append to an existing chunk file
            $uploadPath = $file->getPathname();
            $chunkPath = $this->getChunkPath($file);
            file_put_contents($chunkPath, fopen($uploadPath, 'r'), FILE_APPEND);
            unlink($uploadPath);
            $result->setFile(new File($chunkPath));
            break;

        case UploadResult::CHUNK_END:
            // end of a file, append to an existing chunk file and rename the entire file to the target file name
            $uploadPath = $file->getPathname();
            $chunkPath = $this->getChunkPath($file);
            file_put_contents($chunkPath, fopen($uploadPath, 'r'), FILE_APPEND);
            rename($chunkPath, $uploadPath);
            $result->setFile(new File($uploadPath));
            break;
        default:
            // entire file supplied
            $result->setFile($file);
            break;
        }

        return $result;
    }

    /**
     * Return the path name for the chunked upload of $file in $directory.
     */
    public function getChunkPath(UploadedFile $file)
    {
        return dirname($file->getPathname()).'/'.'chunk-'.md5($file->getClientOriginalName());
    }

    protected function newResult(Request $request, UploadedFile $file)
    {
        //look at the Content-Range header for signs of a chunked
        //upload
        $header = $request->server->get('HTTP_CONTENT_RANGE');
        if (!$header) {
            return new UploadResult($file->getClientOriginalName(), 0, $file->getSize() - 1, $file->getSize());
        }

        // start
        // Content-Range: bytes 0-50000/1000000
        // mid
        // Content-Range: bytes 50001-90000/1000000
        // end
        // Content-Range: bytes 90001-99999/1000000
        $pieces = preg_split('/[^0-9]+/', $header);
        // [
        //     '',
        //     '0', //start
        //     '50000', //end
        //     '1000000' //total size
        // ]

        for ($i = 1; $i < 4; $i++) {
            if (!is_numeric($pieces[$i])) {
                throw new \Exception("Malformed Content-Range header received: $header");
            }
        }

        return new UploadResult($file->getClientOriginalName(), $pieces[1], $pieces[2], $pieces[3]);
    }
}
