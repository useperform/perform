<?php

namespace Perform\MediaBundle\Upload;

use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\Exception\UploadException;

/**
 * Represents a file or chunk of a file that has been uploaded.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class UploadResult
{
    const CHUNK_START = 1;
    const CHUNK_PARTIAL = 2;
    const CHUNK_END = 3;
    const WHOLE = 4;

    protected $clientOriginalName;
    protected $byteStart;
    protected $byteEnd;
    protected $totalBytes;
    protected $chunkStatus;
    protected $file;

    public function __construct($clientOriginalName, $byteStart, $byteEnd, $totalBytes)
    {
        $this->clientOriginalName = $clientOriginalName;
        $this->byteStart = (int) $byteStart;
        $this->byteEnd = (int) $byteEnd;
        $this->totalBytes = (int) $totalBytes;
        $this->chunkStatus = $this->parseChunkStatus();
    }

    private function parseChunkStatus()
    {
        if ($this->byteStart === 0) {
            return $this->byteEnd + 1 === $this->totalBytes ? self::WHOLE : self::CHUNK_START;
        }
        if ($this->byteEnd + 1 === $this->totalBytes) {
            return self::CHUNK_END;
        }

        return self::CHUNK_PARTIAL;
    }

    /**
     * @param File $file
     */
    public function setFile(File $file)
    {
        if ($this->byteEnd + 1 !== $file->getSize()) {
            throw new UploadException(sprintf(
                'The chunked uploaded file is not the expected size. Expected size: bytes 0-%s, %s bytes. Bytes of supplied file: %s. Expected size of this file when fully uploaded: %s.',
                $this->byteEnd,
                $this->byteEnd + 1,
                $file->getSize(),
                $this->totalBytes
            ));
        }

        $this->file = $file;
    }

    /**
     * @return File
     */
    public function getFile()
    {
        return $this->file;
    }

    public function getChunkStatus()
    {
        return $this->chunkStatus;
    }

    public function isComplete()
    {
        return $this->chunkStatus === self::WHOLE || $this->chunkStatus === self::CHUNK_END;
    }

    public function getClientOriginalName()
    {
        return $this->clientOriginalName;
    }

    public function toArray()
    {
        return [
            'clientOriginalName' => $this->clientOriginalName,
            'start' => $this->byteStart,
            'end' => $this->byteEnd,
            'total' => $this->totalBytes,
        ];
    }
}
