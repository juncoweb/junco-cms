<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
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
     * @return array    A numeric array with all the tables in the database.
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
     * @param string $TableName
     * @param string $FromTableName
     * @param bool   $CopyRegisters
     * 
     * @return int
     */
    public function copy(string $TableName, string $FromTableName, bool $CopyRegisters = false): int;

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
     * @param string $CurTableName
     * @param string $NewTableName
     */
    public function rename(string $CurTableName, string $NewTableName): void;

    /**
     * Truncate
     * 
     * @param string|array $TableNames
     */
    public function truncate(string|array $TableNames): void;

    /**
     * Drop
     * 
     * @param string|array $TableNames
     */
    public function drop(string|array $TableNames): void;

    /**
     * Validate Table Name
     * 
     * @param string $tbl_name
     * 
     * @throws \Exception
     */
    public function validateName(string $tbl_name): void;
}
