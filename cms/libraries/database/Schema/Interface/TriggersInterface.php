<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Database\Schema\Interface;

use Database;

interface TriggersInterface
{
    /**
     * Constructor
     */
    public function __construct(Database $db);

    /**
     * Show triggers
     * 
     * @param array $where
     * 
     * @return array
     */
    public function fetchAll(array $where = []): array;

    /**
     * Get trigger data
     * 
     * @param string $Trigger
     * @param array  $db_prefix_tables
     */
    public function showData(string $Trigger, array $db_prefix_tables = []): array;

    /**
     * Create Trigger
     * 
     * @param string $Trigger
     * @param string $Timing
     * @param string $Event
     * @param string $Table
     * @param string $Statement
     * 
     * @return int
     */
    public function create(string $Trigger, string $Timing, string $Event, string $Table, string $Statement): int;

    /**
     * Drop Trigger
     * 
     * @param string|array $TriggerName
     * 
     * @return int
     */
    public function drop(string|array $TriggerName): int;
}
