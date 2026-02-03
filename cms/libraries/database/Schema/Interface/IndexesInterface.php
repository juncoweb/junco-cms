<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Database\Schema\Interface;

use Database;
use Junco\Database\Schema\Interface\Entity\IndexInterface;

interface IndexesInterface
{
    /**
     * Constructor
     */
    public function __construct(Database $db);

    /**
     * Has
     * 
     * @param string $TableName
     * @param string $IndexName
     * 
     * @return bool
     */
    public function has(string $TableName, string $IndexName): bool;

    /**
     * Fetch All
     * 
     * @param string $TableName
     * @param array  $where
     * 
     * @return IndexInterface[]
     */
    public function fetchAll(string $TableName, array $where = []): array;

    /**
     * Fetch
     * 
     * @param string $TableName
     * @param string $IndexName
     * 
     * @return ?IndexInterface
     */
    public function fetch(string $TableName, string $IndexName): ?IndexInterface;

    /**
     * Create
     * 
     * @param IndexInterface $Index
     * 
     * @return int
     */
    public function create(IndexInterface $Index): int;

    /**
     * Drop
     * 
     * @param string $TableName
     * @param string $IndexName
     * 
     * @return int
     */
    public function drop(string $TableName, string $IndexName): int;

    /**
     * New
     * 
     * @param string $TableName
     * @param string $Name
     * 
     * @return IndexInterface
     */
    public function newIndex(string $TableName, string $Name): IndexInterface;
}
