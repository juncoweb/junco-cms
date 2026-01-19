<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Http\Client;

/**
 * Extends the response for easier processing.
 */
trait ClientResponseTrait
{
    // vars
    protected ?string $filename = null;
    protected ?string $extension = null;

    /**
     * Decode the response body as JSON.
     *
     * @param bool $associative
     *
     * @return iterable
     */
    public function getJson(bool $associative = false): iterable
    {
        $result = json_decode($this->getBody(), $associative);

        if (is_iterable($result)) {
            return $result;
        }

        return $associative ? [] : new \stdClass;
    }

    /**
     * Move the response to the target.
     *
     * @param string  $dirpath
     * @param ?string $filename
     *
     * @return int|false
     * 
     * @throws \InvalidArgumentException
     */
    public function moveTo(string $dirpath, ?string &$filename = ''): int|false
    {
        if (!$filename) {
            $filename = $this->getFilename();

            if (!$filename) {
                throw  new \InvalidArgumentException('The filename of the response could not be retrieved');
            }
        }

        is_dir($dirpath) or mkdir($dirpath, SYSTEM_MKDIR_MODE);

        return file_put_contents($dirpath . $filename, $this->getBody());
    }

    /**
     * Return the filename.
     *
     * @return string
     */
    public function getFilename(): string
    {
        if ($this->filename === null) {
            $headerLine = $this->getHeaderLine('Content-Disposition');
            if ($headerLine && preg_match('/filename="(.*?)"/', $headerLine, $match)) {
                $this->filename = $match[1];
                $this->extension = pathinfo($this->filename, PATHINFO_EXTENSION);
            } else {
                $this->filename = '';
                $this->extension = '';
            }
        }

        return $this->filename;
    }

    /**
     * Return the filename.
     *
     * @return string
     */
    public function getExtension(): string
    {
        if ($this->extension === null) {
            $this->getFilename();
        }

        return $this->filename;
    }
}
