<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Database\Adapter;

use mysqli;
use mysqli_sql_exception;
use mysqli_stmt;
use mysqli_result;
use Error;

/**
 * Database Mysqli Adapter
 */
class MysqlAdapter implements AdapterInterface
{
	//
	protected $connection;

	/**
	 * Constructor
	 */
	public function __construct(array $config)
	{
		if (!$config['database.database']) {
			return;
		}
		try {
			mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT); // hack < php 8.1
			$this->connection = new mysqli(
				$config['database.server'],
				$config['database.username'],
				$config['database.password'],
				$config['database.database'],
				$config['database.port'] ?: '3306'
			);

			if ($config['database.charset']) {
				$this->connection->set_charset($config['database.charset']);
			}
		} catch (mysqli_sql_exception $e) {
			throw new Error($e->getMessage(), $e->getCode());
		}
	}

	/**
	 * Checks if a DB connection is active
	 *
	 * @return bool
	 */
	public function isConnected(): bool
	{
		return $this->connection && !$this->connection->connect_error;
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
		return "'" . $this->connection->real_escape_string($value) . "'";
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
		try {
			return new MysqliStatement($this->connection->prepare($query));
		} catch (mysqli_sql_exception $e) {
			throw new Error($e->getMessage(), $e->getCode());
		}
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
		try {
			return new MysqliResult($this->connection->query($query));
		} catch (mysqli_sql_exception $e) {
			throw new Error($e->getMessage(), $e->getCode());
		}
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
		try {
			$this->connection->query($query);

			return $this->connection->affected_rows;
		} catch (mysqli_sql_exception $e) {
			throw new Error($e->getMessage(), $e->getCode());
		}
	}

	/**
	 * Get the last ID inserted
	 * 
	 * @return int|string
	 */
	public function lastInsertId(): int|string
	{
		return $this->connection->insert_id;
	}

	/**
	 * Get info
	 * 
	 * @return array
	 */
	public function getInfo(): array
	{
		$data = $this->connection->get_charset();
		return [
			'client'	=> $this->connection->client_info,
			'host'		=> $this->connection->host_info,
			'server'	=> $this->connection->server_info,
			'charset'	=> $data->charset,
			'collation'	=> $data->collation,
		];
	}

	/**
	 * Resolve Schema
	 * 
	 * @return string
	 */
	public function resolveSchema(): string
	{
		return \Junco\Database\Schema\MysqlSchema::class;
	}

	/**
	 * Destructor
	 *
	 * Closes the DB connection when this object is destroyed.
	 *
	 */
	public function __destruct()
	{
		if ($this->connection) {
			$this->connection->close();

			$this->connection = null;
		}
	}
}

/**
 * Database Statement
 */
class MysqliStatement implements StatementInterface
{
	// vars
	protected $stmt;

	/**
	 * Constructor
	 */
	public function __construct(mysqli_stmt $stmt)
	{
		$this->stmt = $stmt;
	}

	/**
	 * Bind param
	 *
	 * @param object $stmt    The mysqli_stmt object.
	 * @param array  $params  An array with the values to pass.
	 */
	protected function bindParam($params): void
	{
		$types = '';
		foreach ($params as &$param) {
			if (is_float($param)) {
				$types .= 'd';
			} elseif (is_int($param)) {
				$types .= 'i';
			} elseif (is_string($param)) {
				$types .= 's';
			} else {
				$types .= 'b';
			}
		}
		array_unshift($params, $types);
		call_user_func_array([$this->stmt, 'bind_param'], $params);
	}

	/**
	 * Executes a prepared query
	 *
	 * @param array $params  An array with the values to pass.
	 */
	public function execute(array $params = []): void
	{
		try {
			if ($params) {
				$this->bindParam($params);
			}
			$this->stmt->execute();
		} catch (mysqli_sql_exception $e) {
			// I change the exception for an error.
			throw new Error($e->getMessage(), $e->getCode());
		}
	}

	/**
	 * Get result
	 *
	 * @return ResultInterface
	 */
	public function getResult(): ResultInterface
	{
		return new MysqliResult($this->stmt->get_result());
	}

	/**
	 * Count affected
	 * 
	 * @return int
	 */
	public function countAffected(): int
	{
		return $this->stmt->affected_rows;
	}

	/**
	 * Destruct
	 */
	public function __destruct()
	{
		$this->stmt->close();
	}
}

/**
 * Database Result
 */
class MysqliResult implements ResultInterface
{
	// vars
	protected $result = null;

	/**
	 * Constructor
	 * 
	 * @param mysqli_result $result
	 */
	public function __construct(mysqli_result $result)
	{
		$this->result = $result;
	}

	/**
	 * Returns a single row
	 *
	 * @return array|false
	 */
	public function fetch($style = \Database::FETCH_ASSOC): array|false
	{
		return $this->result->fetch_array($style) ?: false;
	}

	/**
	 * Returns a single row
	 *
	 * @return object|false
	 */
	public function fetchObject(string $class_name = 'stdClass', array $ctor_args = []): object|false
	{
		return $this->result->fetch_object($class_name, $ctor_args);
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
		$row = $this->result->fetch_row();
		if ($row) {
			return $row[$column_number];
		}

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
		if ($this->result) {
			if ($style == \Database::FETCH_COLUMN) {
				if (is_array($index)) {
					$key   = array_key_first($index);
					$index = $index[$key];

					while ($row = $this->result->fetch_array(MYSQLI_BOTH)) {
						$rows[$row[$key]] = $row[$index];
					}
				} else {
					$index ??= 0;

					while ($row = $this->result->fetch_array(MYSQLI_BOTH)) {
						$rows[] = $row[$index];
					}
				}
			} elseif ($index === null) {
				while ($row = $this->result->fetch_array($style)) {
					$rows[] = $row;
				}
			} else {
				while ($row = $this->result->fetch_array($style)) {
					$rows[$row[$index]] = $row;
				}
			}
		}

		return $rows;
	}

	/**
	 * Destruct
	 */
	public function __destruct()
	{
		if ($this->result) {
			$this->result->free();
		}
	}
}
