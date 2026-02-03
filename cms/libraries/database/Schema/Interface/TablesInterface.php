<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Database\Schema\Interface;

use Junco\Database\Schema\Interface\Entity\TableInterface;
use Database;

interface TablesInterface
{
    /**
     * Constructor
     */
    public function __construct(Database $db);

    /**
     * Has
     * 
     * @param string $TableName
     * 
     * @return bool
     */
    public function has(string $TableName): bool;

    /**
     * Fetch All
     * 
     * @param array $where
     * 
     * @return array
     */
    public function fetchAll(array $where = []): array;

    /**
     * Fetch
     * 
     * @param string $TableName
     * 
     * @return ?TableInterface
     */
    public function fetch(string $TableName): ?TableInterface;

    /**
     * List
     * 
     * @return array    An associative array of all tables.
     */
    public function list(): array;

    /**
     * New
     * 
     * @param string $Name
     * 
     * @return TableInterface
     */
    public function newTable(string $Name): TableInterface;

    /**
     * Create
     * 
     * @param TableInterface $Table
     * 
     * @return int
     */
    public function create(TableInterface $Table): int;

    /**
     * Copy
     * 
     * @param string $FromTableName
     * @param string $ToTableName
     * @param bool   $CopyRegisters
     * 
     * @return int
     */
    public function copy(string $FromTableName, string $ToTableName, bool $CopyRegisters = false): int;

    /**
     * Alter
     * 
     * @param TableInterface $Table
     * 
     * @return int
     */
    public function alter(TableInterface $Table): int;

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
     * From
     * 
     * @param array $Data
     * 
     * @return ?TableInterface
     */
    public function from(array $Data): ?TableInterface;

    /**
     * Validate table name
     * 
     * @param string $tbl_name
     * 
     * @throws \Exception
     */
    public function validateName(string $tbl_name): void;
}
