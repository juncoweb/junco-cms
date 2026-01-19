<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Database\Schema\Interface;

use Database;

interface ForeignKeysInterface
{
    /**
     * Constructor
     */
    public function __construct(Database $db);

    /**
     * Has
     * 
     * @param string $TableName
     * @param string $Name
     * 
     * @return bool
     */
    public function has(string $TableName, string $Name): bool;

    /**
     * Foreign keys
     * 
     * @param array $where
     * 
     * @return array
     */
    public function fetchAll(array $where = []): array;

    /**
     * Show
     * 
     * @param string $TableName
     * @param string $Name
     * 
     * @return array
     */
    public function showData(string $TableName, string $Name): ?array;

    /**
     * Create
     * 
     * @param string $TableName
     * @param string $Name
     * @param string $ColumName
     * @param string $ReferencedTableName
     * @param string $ReferencedColumnName
     * @param string $DeleteRule
     * @param string $UpdateRule
     * 
     * @return int
     */
    public function create(
        string $TableName,
        string $Name,
        string $ColumName,
        string $ReferencedTableName,
        string $ReferencedColumnName,
        string $DeleteRule = 'RESTRICT',
        string $UpdateRule = 'RESTRICT'
    ): int;

    /**
     * Drop
     * 
     * @param string       $TableName
     * @param string|array $Name
     * 
     * @return int
     */
    public function drop(string $TableName, string|array $Name): int;

    /**
     * Get
     * 
     * @return array
     */
    public function getRules(): array;
}
