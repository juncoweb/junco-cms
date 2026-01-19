<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Database\Adapter;

/**
 * Adapter
 */
interface AdapterInterface
{
    /**
     * Checks if a DB connection is active
     *
     * @return bool
     */
    public function isConnected(): bool;

    /**
     * Escape a value that will be included in a query string
     *
     * @param int|string $value    The value to escape.
     *
     * @return string
     */
    public function quote(string $value): string;

    /**
     * Prepare an SQL statement for execution
     * 
     * @param string $query
     * 
     * @return StatementInterface
     */
    public function prepare(string $query): StatementInterface;

    /**
     * Query
     * 
     * @param string $query
     * 
     * @return ResultInterface
     */
    public function query(string $query): ResultInterface;

    /**
     * Exec
     * 
     * @param string $query
     * 
     * @return int
     */
    public function exec(string $query): int;

    /**
     * Get the last ID inserted
     * 
     * @return int|string
     */
    public function lastInsertId(): int|string;

    /**
     * Get info
     * 
     * @return array
     */
    public function getInfo(): array;

    /**
     * Resolve Schema
     * 
     * @return string
     */
    public function resolveSchema(): string;
}


/**
 * Database Statement
 */
interface StatementInterface
{
    /**
     * Executes a prepared query
     *
     * @param array $params  An array with the values to pass.
     */
    public function execute(array $params = []): void;

    /**
     * Get result
     *
     * @return ResultInterface
     */
    public function getResult(): ResultInterface;

    /**
     * Count affected
     * 
     * @return int
     */
    public function countAffected(): int;
}

/**
 * Result interface
 */
interface ResultInterface
{
    /**
     * Returns a single row
     *
     * @return array|false
     */
    public function fetch($style = \Database::FETCH_ASSOC): array|false;

    /**
     * Returns a single row
     *
     * @return object|false
     */
    public function fetchObject(string $class_name = 'stdClass', array $ctor_args = []): object|false;

    /**
     * Returns a single column from the next row of a result set
     *
     * @param int  $column_number
     *
     * @return string
     */
    public function fetchColumn(int $column_number = 0): string|false|null;

    /**
     * Returns all rows
     *
     * @param int   $style
     * @param mixed $index
     * @param array $rows
     * 
     * @return array
     */
    public function fetchAll(int $style = \Database::FETCH_ASSOC, $index = null, array $rows = []): array;
}
