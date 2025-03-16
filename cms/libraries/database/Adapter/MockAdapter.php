<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Database\Adapter;

/**
 * Database Mock Adapter
 */
class MockAdapter implements AdapterInterface
{
	//
	protected $connection;

	/**
	 * Constructor
	 */
	public function __construct(array $config) {}

	/**
	 * Checks if a DB connection is active
	 *
	 * @return bool
	 */
	public function isConnected(): bool
	{
		return true;
	}

	/**
	 * Escape a value that will be included in a query string
	 *
	 * @param int|string $value    The value to escape.
	 *
	 * @return string
	 */
	public function quote(string $value): string
	{
		return "'" . $value . "'";
	}

	/**
	 * Prepare an SQL statement for execution
	 * 
	 * @param string $query
	 * 
	 * @return StatementInterface
	 */
	public function prepare(string $query): StatementInterface
	{
		return new MockStatement($query);
	}

	/**
	 * Query
	 * 
	 * @param string $query
	 * 
	 * @return ResultInterface
	 */
	public function query(string $query): ResultInterface
	{
		return new MockResult($query);
	}

	/**
	 * Exec
	 * 
	 * @param string $query
	 * 
	 * @return int
	 */
	public function exec(string $query): int
	{
		return 1;
	}

	/**
	 * Get the last ID inserted
	 * 
	 * @return int|string
	 */
	public function lastInsertId(): int|string
	{
		return 0;
	}

	/**
	 * Get info
	 * 
	 * @return array
	 */
	public function getInfo(): array
	{
		return [
			'client'	=> 'client_mock',
			'host'		=> 'host_mock',
			'server'	=> 'server_mock',
			'charset'	=> 'utf8',
			'collation'	=> 'utf8_general_ci',
		];
	}

	/**
	 * Resolve Schema
	 * 
	 * @return string
	 */
	public function resolveSchema(): string
	{
		return \Junco\Database\Schema\MockSchema::class;
	}
}

/**
 * Database Statement
 */
class MockStatement implements StatementInterface
{
	// vars
	protected $query;
	protected $params;

	/**
	 * Constructor
	 */
	public function __construct(string $query)
	{
		$this->query = $query;
	}

	/**
	 * Executes a prepared query
	 *
	 * @param array $params  An array with the values to pass.
	 */
	public function execute(array $params = []): void
	{
		$this->params = $params;
	}

	/**
	 * Get result
	 *
	 * @return ResultInterface
	 */
	public function getResult(): ResultInterface
	{
		return new MockResult($this->query, $this->params);
	}

	/**
	 * Count affected
	 * 
	 * @return int
	 */
	public function countAffected(): int
	{
		return 0;
	}
}

/**
 * Database Result
 */
class MockResult implements ResultInterface
{
	// vars
	protected $query = null;
	protected $params = null;

	/**
	 * Constructor
	 * 
	 * @param string $query
	 */
	public function __construct(string $query, array $params = [])
	{
		$this->query = $query;
		$this->params = $params;
	}

	/**
	 * Returns a single row
	 *
	 * @return array|false
	 */
	public function fetch($style = \Database::FETCH_ASSOC): array|false
	{
		return false;
	}

	/**
	 * Returns a single row
	 *
	 * @return object|false
	 */
	public function fetchObject(string $class_name = 'stdClass', array $ctor_args = []): object|false
	{
		return false;
	}

	/**
	 * Returns a single column from the next row of a result set
	 *
	 * @param int  $column_number
	 *
	 * @return string
	 */
	public function fetchColumn(int $column_number = 0): string|false|null
	{
		return false;
	}

	/**
	 * Returns all rows
	 *
	 * @param int   $style
	 * @param mixed $index
	 * @param array $rows
	 * 
	 * @return array
	 */
	public function fetchAll(int $style = \Database::FETCH_ASSOC, $index = null, array $rows = []): array
	{
		return $rows;
	}

	/**
	 * Returns all rows
	 *
	 * @param int   $style
	 * @param mixed $index
	 * @param array $rows
	 * 
	 * @return array
	 */
	public function getParams(): array
	{
		return $this->params;
	}
}
