<?php

namespace Admin\MediaBundle\Upload;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\Exception\UploadException;

/**
 * UploadHandler
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 */
class UploadHandler
{
    protected $completed = [];
    protected $chunks = [];
    protected $request;

    const CHUNK_START = 1;
    const CHUNK_PARTIAL = 2;
    const CHUNK_END = 3;
    const WHOLE = 4;

    /**
     * Examine an uploaded file. If the file is a chunk of a bigger
     * file, either create a new file or append it onto the file.
     *
     * @param Request
     * @param UploadedFile
     */
    public function process(Request $request, UploadedFile $file)
    {
        if (!$file->isValid()) {
            throw new UploadException($file->getErrorMessage());
        }

        $status = $this->chunkStatus($request, $file);

        if ($status === self::WHOLE) {
            $this->completed[$file->getClientOriginalName()] = new File($file->getPathname());

            return;
        }

        if ($status === self::CHUNK_START) {
            $path = $this->getChunkPath($file);
            $this->chunks[$file->getClientOriginalName()] =  $file->move(dirname($path), basename($path));

            return;
        }

        if ($status === self::CHUNK_PARTIAL) {
            $chunk = $this->getChunkPath($file);
            $upload = $file->getPathname();
            file_put_contents($chunk, fopen($upload, 'r'), FILE_APPEND);

            unlink($upload);
            $this->chunks[$file->getClientOriginalName()] = new File($chunk);

            return;
        }

        if ($status === self::CHUNK_END) {
            $chunk = $this->getChunkPath($file);
            $upload = $file->getPathname();
            file_put_contents($chunk, fopen($upload, 'r'), FILE_APPEND);

            rename($chunk, $upload);
            $this->completed[$file->getClientOriginalName()] = new File($upload);
            unset($this->chunks[$file->getClientOriginalName()]);

            return;
        }
    }

    /**
     * Return the path name for the chunked upload of $file in $directory.
     */
    public function getChunkPath(UploadedFile $file)
    {
        return dirname($file->getPathname()).'/'.'chunk-'.md5($file->getClientOriginalName());
    }

    /**
     * Decide whether the uploaded file is a complete file or the
     * beginning, middle or end of a chunked upload.
     */
    protected function chunkStatus(Request $request)
    {
        //look at the Content-Range header for signs of a chunked
        //upload
        $header = $request->server->get('HTTP_CONTENT_RANGE');
        if (!$header) {
            return self::WHOLE;
        }

        //Content-Range: bytes 0-50000/1000000
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

        $chunk_size = $pieces[2] - $pieces[1];
        $total_size = $pieces[3];
        if ($chunk_size === $total_size) {
            return self::WHOLE;
        }
        if ((int) $pieces[1] === 0) {
            return self::CHUNK_START;
        }
        if ((int) $pieces[2] + 1 === (int) $pieces[3]) {
            return self::CHUNK_END;
        }

        return self::CHUNK_PARTIAL;
    }

    /**
     * Get all files that were successfully processed.
     */
    public function getUploadedFiles()
    {
        return array_merge($this->completed, $this->chunks);
    }

    /**
     * Get all files that have only partially uploaded.
     */
    public function getPartialFiles()
    {
        return $this->chunks;
    }

    /**
     * Get all files that have uploaded completely.
     */
    public function getCompletedFiles()
    {
        return $this->completed;
    }
}
