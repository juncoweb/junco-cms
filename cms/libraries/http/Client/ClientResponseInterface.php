<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Http\Client;

use Psr\Http\Message\ResponseInterface;

/**
 * Extends the response for easier processing.
 */
interface ClientResponseInterface extends ResponseInterface
{
    /**
     * Decode the response body as JSON.
     *
     * @param bool $associative
     *
     * @return iterable
     */
    public function getJson(bool $associative = false): iterable;

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
    public function moveTo(string $dirpath, ?string &$filename = ''): int|false;

    /**
     * Return the filename.
     *
     * @return string
     */
    public function getFilename(): string;

    /**
     * Return the filename.
     *
     * @return string
     */
    public function getExtension(): string;
}
