<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Responder\Contract;

interface HttpBlankInterface extends ResponderInterface
{
    /**
     * Create File
     * 
     * @param string $content
     * @param string $filename
     * @param string $ContentType
     * @param string $extension
     */
    public function createFile(
        string $content,
        string $filename = '',
        string $ContentType = '',
        string $extension = ''
    ): void;

    /**
     * Create Json File
     * 
     * @param array  $json
     * @param string $filename
     */
    public function createJsonFile(array $json, string $filename = ''): void;

    /**
     * Create CSV File
     * 
     * @param array  $rows
     * @param string $filename
     */
    public function createCSVFile(array $rows, string $filename = ''): void;

    /**
     * Create SQL File
     * 
     * @param string $sql
     * @param string $filename
     */
    public function createSqlFile(string $sql, string $filename = ''): void;

    /**
     * Adds a file from its path.
     * 
     * @param string $filepath
     * @param string $filename
     * 
     * @return bool
     */
    public function appendFile(string $filepath, string $filename = ''): bool;
}
