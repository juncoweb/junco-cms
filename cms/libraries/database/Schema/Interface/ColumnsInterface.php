<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Database\Schema\Interface;

use Junco\Database\Schema\Interface\Entity\ColumnInterface;
use Database;

interface ColumnsInterface
{
    /**
     * Constructor
     */
    public function __construct(Database $db);

    /**
     * Fetch all
     * 
     * @param string $TableName
     * @param array  $where
     * 
     * @return ColumnInterface[]
     */
    public function fetchAll(string $TableName, array $where = []): array;

    /**
     * Fetch
     * 
     * @param string $TableName
     * @param string $ColumnName
     * 
     * @return ?ColumnInterface
     */
    public function fetch(string $TableName, string $ColumnName): ?ColumnInterface;

    /**
     * Get Columns
     * 
     * @param string $TableName
     * @param array  $where
     * 
     * @return array
     */
    public function list(string $TableName, array $where = []): array;

    /**
     * Alter
     * 
     * @param ColumnInterface $column
     * 
     * @return int
     */
    public function alter(ColumnInterface $column): int;

    /**
     * Drop
     * 
     * @param string		$TableName
     * @param string|array	$ColumnNames
     * 
     * @return int
     */
    public function drop(string $TableName, string|array $ColumnNames): int;
}
