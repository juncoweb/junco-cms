<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Http\Message;

use Psr\Http\Message\UploadedFileInterface;
use Psr\Http\Message\StreamInterface;

/**
 * Value object representing a file uploaded through an HTTP request.
 */
class UploadedFile implements UploadedFileInterface
{
    // vars
    protected ?string            $file                = null;
    protected ?StreamInterface    $stream                = null;
    protected ?string            $clientFilename     = null;
    protected ?string            $clientMediaType    = null;
    protected ?int                $size;
    protected int                 $error                = 0;
    protected bool                 $moved                = false;

    // const
    protected const UPLOAD_ERRORS = [
        UPLOAD_ERR_OK,
        UPLOAD_ERR_INI_SIZE,
        UPLOAD_ERR_FORM_SIZE,
        UPLOAD_ERR_PARTIAL,
        UPLOAD_ERR_NO_FILE,
        UPLOAD_ERR_NO_TMP_DIR,
        UPLOAD_ERR_CANT_WRITE,
        UPLOAD_ERR_EXTENSION,
    ];

    /**
     * Constructor
     * 
     * @param StreamInterface|string|resource $input
     * @param ?int $size
     * @param int $errorStatus
     * @param string $clientFilename
     * @param string $clientMediaType
     * 
     * @throws \InvalidArgumentException
     */
    public function __construct(
        $input,
        ?int $size,
        int $errorStatus,
        ?string $clientFilename = null,
        ?string $clientMediaType = null
    ) {
        if (!in_array($errorStatus, self::UPLOAD_ERRORS, true)) {
            throw new \InvalidArgumentException('Invalid error status for UploadedFile');
        }

        $this->error            = $errorStatus;
        $this->size                = $size;
        $this->clientFilename    = $clientFilename;
        $this->clientMediaType    = $clientMediaType;

        if ($this->error === UPLOAD_ERR_OK) {
            if (is_string($input)) {
                $this->file = $input;
            } elseif ($input instanceof StreamInterface) {
                $this->stream = $input;
            } else {
                $this->stream = new Stream($input);
            }
        }
    }

    /**
     * Retrieve a stream representing the uploaded file.
     * 
     * @return StreamInterface Stream representation of the uploaded file.
     * 
     * @throws \RuntimeException in cases when no stream is available or can be created.
     */
    public function getStream(): StreamInterface
    {
        $this->verifyIsActive();

        if ($this->stream === null) {
            $this->stream = new Stream($this->file);
        }

        return $this->stream;
    }

    /**
     * Move the uploaded file to a new location.
     * 
     * @param string $targetPath Path to which to move the uploaded file.
     * 
     * @throws \InvalidArgumentException if the $targetPath specified is invalid.
     * @throws \RuntimeException on any error during the move operation.
     */
    public function moveTo(string $targetPath): void
    {
        $this->verifyIsActive();

        if (!$targetPath) {
            throw new \InvalidArgumentException('Invalid path provided for move operation');
        }

        if ($this->file) {
            $result = defined('STDIN')
                ? rename($this->file, $targetPath)
                : move_uploaded_file($this->file, $targetPath);

            if ($result === false) {
                throw new \RuntimeException(sprintf('Uploaded file could not be status to «%s»', $targetPath));
            }
        } else {
            $handler = fopen($targetPath, 'wb+');
            if ($handler === false) {
                throw new \RuntimeException(sprintf('Uploaded file could not be status to «%s»', $targetPath));
            }
            $this->stream->rewind();

            while (!$this->stream->eof()) {
                fwrite($handler, $this->stream->read(8192));
            }

            fclose($handler);
            $this->stream->close();
        }

        $this->moved = true;
    }

    /**
     * Retrieve the file size.
     *
     * @return int|null The file size in bytes or null if unknown.
     */
    public function getSize(): ?int
    {
        return $this->size;
    }

    /**
     * Retrieve the error associated with the uploaded file.
     *
     * @see http://php.net/manual/en/features.file-upload.errors.php
     * 
     * @return int One of PHP's UPLOAD_ERR_XXX constants.
     */
    public function getError(): int
    {
        return $this->error;
    }

    /**
     * Retrieve the filename sent by the client.
     *
     * @return string|null
     */
    public function getClientFilename(): ?string
    {
        return $this->clientFilename;
    }

    /**
     * Retrieve the media type sent by the client.
     *
     * @return string|null
     */
    public function getClientMediaType(): ?string
    {
        return $this->clientMediaType;
    }

    /**
     * Verify Is Active
     * 
     * @throws \RuntimeException if is moved or not ok
     */
    protected function verifyIsActive(): void
    {
        if ($this->error !== UPLOAD_ERR_OK) {
            throw new \RuntimeException('Cannot retrieve stream due to upload error');
        }
        if ($this->moved) {
            throw new \RuntimeException('Cannot retrieve stream after it has already been status');
        }
    }
}
