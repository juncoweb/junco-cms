<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Database\Schema\Interface;

use Database;

interface TablesInterface
{
    /**
     * Constructor
     */
    public function __construct(Database $db);

    /**
     * Has table
     * 
     * @param string $tbl_name
     * 
     * @return bool
     */
    public function has(string $tbl_name): bool;

    /**
     * Fetch All
     * 
     * @param array $where
     * 
     * @return array
     */
    public function fetchAll(array $where = []): array;

    /**
     * List
     * 
     * @return array    An associative array of all tables.
     */
    public function list(): array;

    /**
     * Show
     * 
     * @param string $tbl_name
     * @param string $tbl_name
     * @param bool   $add_if_not_exists
     * @param bool   $add_auto_increment
     * @param bool   $set_db_prefix = false
     * 
     * @return array
     */
    public function showData(
        string $tbl_name,
        bool   $add_if_not_exists = false,
        bool   $add_auto_increment = false,
        bool   $set_db_prefix = false
    ): array;

    /**
     * Create Table
     * 
     * @param string $TableName
     * @param array  $Table
     * 
     * @return int
     */
    public function create(string $TableName, array $Table): int;

    /**
     * Copy Table
     * 
     * @param string $FromTableName
     * @param string $ToTableName
     * @param bool   $CopyRegisters
     * 
     * @return int
     */
    public function copy(string $FromTableName, string $ToTableName, bool $CopyRegisters = false): int;

    /**
     * Alter Table
     * 
     * @param string $TableName
     * @param array  $Table
     * 
     * @return int
     */
    public function alter(string $TableName, array $Table): int;

    /**
     * Rename
     * 
     * @param string $FromTableName
     * @param string $ToTableName
     * 
     * @return int
     */
    public function rename(string $FromTableName, string $ToTableName): int;

    /**
     * Truncate
     * 
     * @param string|array $TableNames
     * 
     * @return int
     */
    public function truncate(string|array $TableNames): int;

    /**
     * Drop
     * 
     * @param string|array $TableNames
     * 
     * @return int
     */
    public function drop(string|array $TableNames): int;

    /**
     * Validate Table Name
     * 
     * @param string $tbl_name
     * 
     * @throws \Exception
     */
    public function validateName(string $tbl_name): void;
}
