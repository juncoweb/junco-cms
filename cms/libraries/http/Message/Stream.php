<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Http\Message;

use Psr\Http\Message\StreamInterface;
use Stringable;

/**
 * Describes a data stream.
 */
class Stream implements Stringable, StreamInterface
{
    // vars
    protected $stream;
    protected bool $seekable;
    protected bool $readable;
    protected bool $writable;

    /**
     * Constructor
     * 
     * @param string|resource $stream
     * @param string          $mode Mode with which to open stream
     * 
     * @throws \InvalidArgumentException
     */
    public function __construct($stream, string $mode = 'r')
    {
        if (is_string($stream)) {
            $stream = fopen($stream, $mode);

            if (!$stream) {
                throw new \InvalidArgumentException('Invalid stream provided');
            }
        } elseif (!is_resource($stream) || get_resource_type($stream) != 'stream') {
            throw new \InvalidArgumentException('Invalid stream provided');
        }

        $this->stream = $stream;

        $meta = stream_get_meta_data($this->stream);
        $this->seekable = $meta['seekable'];
        $this->readable = (bool)preg_match('/r|a\+|ab\+|w\+|wb\+|x\+|xb\+|c\+|cb\+/', $meta['mode']);
        $this->writable = (bool)preg_match('/a|w|r\+|rb\+|rw|x|c/', $meta['mode']);
    }

    /**
     * Reads all data from the stream into a string, from the beginning to end.
     *
     * @see http://php.net/manual/en/language.oop5.magic.php#object.tostring
     * 
     * @return string
     */
    public function __toString(): string
    {
        try {
            if ($this->seekable) {
                $this->seek(0);
            }

            return $this->getContents();
        } catch (\Throwable $e) {
            return '';
        }
    }

    /**
     * Destruct
     */
    public function __destruct()
    {
        $this->close();
    }

    /**
     * Closes the stream and any underlying resources.
     *
     * @return void
     */
    public function close(): void
    {
        if (!$this->stream) {
            return;
        }

        $resource = $this->detach();
        fclose($resource);
    }

    /**
     * Separates any underlying resources from the stream.
     *
     * @return resource|null Underlying PHP stream, if any
     */
    public function detach()
    {
        $resource = $this->stream;
        $this->stream = null;
        $this->readable =
            $this->writable =
            $this->seekable = false;

        return $resource;
    }

    /**
     * Get the size of the stream if known.
     * 
     * @return int|null
     */
    public function getSize(): ?int
    {
        if ($this->stream === null) {
            return null;
        }

        $stats = fstat($this->stream);
        if ($stats === false) {
            return null;
        }

        return $stats['size'];
    }

    /**
     * Returns the current position of the file read/write pointer
     *
     * @return int Position of the file pointer
     * 
     * @throws \RuntimeException on failure.
     */
    public function tell(): int
    {
        if (!$this->stream) {
            throw new \RuntimeException('Error occurred during tell operation');
        }

        $result = ftell($this->stream);

        if (!is_int($result)) {
            throw new \RuntimeException('Error occurred during tell operation');
        }

        return $result;
    }

    /**
     * Returns true if the stream is at the end of the stream.
     *
     * @return bool
     */
    public function eof(): bool
    {
        if (!$this->stream) {
            return true;
        }

        return feof($this->stream);
    }

    /**
     * Returns whether or not the stream is seekable.
     *
     * @return bool
     */
    public function isSeekable(): bool
    {
        if (!$this->stream) {
            return false;
        }

        return $this->seekable;
    }

    /**
     * Seek to a position in the stream.
     *
     * @link http://www.php.net/manual/en/function.fseek.php
     * @param int $offset Stream offset
     * @param int $whence Specifies how the cursor position will be calculated
     *     based on the seek offset. Valid values are identical to the built-in
     *     PHP $whence values for `fseek()`.  SEEK_SET: Set position equal to
     *     offset bytes SEEK_CUR: Set position to current location plus offset
     *     SEEK_END: Set position to end-of-stream plus offset.
     * 
     * @throws \RuntimeException on failure.
     */
    public function seek(int $offset, int $whence = SEEK_SET): void
    {
        if (!$this->seekable) {
            throw new \RuntimeException('Stream is not seekable');
        }

        $result = fseek($this->stream, $offset, $whence);

        if (0 !== $result) {
            throw new \RuntimeException('Error seeking within stream');
        }
    }

    /**
     * Seek to the beginning of the stream.
     *
     * @throws \RuntimeException on failure.
     */
    public function rewind(): void
    {
        $this->seek(0);
    }

    /**
     * Returns whether or not the stream is writable.
     *
     * @return bool
     */
    public function isWritable(): bool
    {
        if (!$this->stream) {
            return false;
        }

        return $this->writable;
    }

    /**
     * Write data to the stream.
     * 
     * @param string $string The string that is to be written.
     * 
     * @return int Returns the number of bytes written to the stream.
     * 
     * @throws \RuntimeException on failure.
     */
    public function write(string $string): int
    {
        if (!$this->writable) {
            throw new \RuntimeException('Error writing to stream');
        }

        $result = fwrite($this->stream, $string);

        if (false === $result) {
            throw new \RuntimeException('Error writing to stream');
        }

        return $result;
    }

    /**
     * Returns whether or not the stream is readable.
     *
     * @return bool
     */
    public function isReadable(): bool
    {
        return $this->readable;
    }

    /**
     * Read data from the stream.
     *
     * @param int $length Read up to $length bytes from the object and return them.
     * 
     * @return string Returns the data read from the stream.
     * 
     * @throws \RuntimeException if an error occurs.
     */
    public function read(int $length): string
    {
        if (!$this->readable) {
            throw new \RuntimeException('Error reading stream');
        }

        $result = fread($this->stream, $length);

        if (false === $result) {
            throw new \RuntimeException('Error reading stream');
        }

        return $result;
    }

    /**
     * Returns the remaining contents in a string
     *
     * @return string
     * 
     * @throws \RuntimeException if an error occurs.
     */
    public function getContents(): string
    {
        if (!$this->readable) {
            throw new \RuntimeException('Error reading stream');
        }

        $result = stream_get_contents($this->stream);
        if (false === $result) {
            throw new \RuntimeException('Error reading stream');
        }

        return $result;
    }

    /**
     * Get stream metadata as an associative array or retrieve a specific key.
     *
     * The keys returned are identical to the keys returned from PHP's
     * stream_get_meta_data() function.
     *
     * @param string|null $key Specific metadata to retrieve.
     * 
     * @return array|mixed|null
     */
    public function getMetadata(?string $key = null)
    {
        $metadata = stream_get_meta_data($this->stream);

        if (null === $key) {
            return $metadata;
        }

        return $metadata[$key] ?? null;
    }
}
