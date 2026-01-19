<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Logger\Adapter;

use Junco\Logger\Enum\LogLevel;
use Junco\Logger\Enum\LogStatus;

interface AdapterInterface
{
    /**
     * Gets all logs
     *
     * @return array
     */
    public function fetchAll(array $where = []): array;

    /**
     * Fetch
     *
     * @param string $id
     * 
     * @return array|null
     */
    public function fetch(string $id): array|null;

    /**
     * Stores a log
     *
     * @param LogLevel $level
     * @param string   $message
     * @param array    $context
     * 
     * @return bool
     */
    public function log(LogLevel $level, string $message, array $context): bool;

    /**
     * Toggle the status of the registers.
     * 
     * @param string[]   $id
     * @param ?LogStatus $status
     * 
     * @return bool
     */
    public function status(array $id, ?LogStatus $status = null): bool;

    /**
     * Delete
     * 
     * @param string[] $id
     *
     * @return bool
     */
    public function deleteMultiple(array $id): bool;

    /**
     * Stores the complete list of logs
     *
     * @param array $logs  All the logs to save.
     *
     * @return bool
     */
    public function storeAll(array $logs = []): bool;
}
