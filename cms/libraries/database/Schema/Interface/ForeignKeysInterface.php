<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Database\Schema\Interface;

use Database;
use Junco\Database\Schema\Interface\Entity\ForeignKeyInterface;

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
     * @param string $TableName
     * @param array  $where
     * 
     * @return ForeignKey[]
     */
    public function fetchAll(string $TableName, array $where = []): array;

    /**
     * Fetch
     * 
     * @param string $TableName
     * @param string $Name
     * 
     * @return ?ForeignKey
     */
    public function fetch(string $TableName, string $Name): ?ForeignKeyInterface;

    /**
     * Create
     * 
     * @param ForeignKeyInterface $ForeignKey
     * 
     * @return int
     */
    public function create(ForeignKeyInterface $ForeignKey): int;

    /**
     * Drop
     * 
     * @param string       $TableName
     * @param string|array $Names
     * 
     * @return int
     */
    public function drop(string $TableName, string|array $Names): int;

    /**
     * New
     * 
     * @param string $Name
     * @param string $TableName
     * @param string $ColumName
     * 
     * @return ForeignKeyInterface
     */
    public function newForeignKey(string $Name, string $TableName, string $ColumName): ForeignKeyInterface;

    /**
     * Get
     * 
     * @return array
     */
    public function getRules(): array;
}
