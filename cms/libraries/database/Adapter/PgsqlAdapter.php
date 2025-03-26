<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Database\Adapter;

use PgSql;
use Error;

/**
 * Database Postgre Adapter
 */
class PgsqlAdapter implements AdapterInterface
{
    // trait
    use PgsqlTrait;

    //
    protected $connection;

    /**
     * Constructor
     */
    public function __construct(
        string $server,
        string $username,
        string $password,
        string $database,
        string $port = '',
        string $charset = ''
    ) {
        if (!$port) {
            $port = '5432';
        }
        if (!$charset) {
            $charset = 'utf8';
        }
        $this->connection = pg_connect(
            'host=' . $server . ' '
                . 'port=' . $port . ' '
                . 'user=' . $username . ' '
                . 'password=' . $password . ' '
                . 'dbname=' . $database . ' '
                . 'options=\'--client_encoding=' . $charset . '\' '
        );

        if (!$this->connection) {
            throw new Error('Could not make a database link');
        }

        pg_query($this->connection, "SET CLIENT_ENCODING TO '$charset'");
    }

    /**
     * Checks if a DB connection is active
     *
     * @return bool
     */
    public function isConnected(): bool
    {
        return pg_connection_status($this->connection) == PGSQL_CONNECTION_OK;
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
        return "'" . pg_escape_string($this->connection, $value) . "'";
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
        $this->sanitizeQueryToNativePg($query);

        $stmt = pg_prepare($this->connection, '', $query);
        if (!$stmt) {
            throw new Error(pg_last_error($this->connection));
        }
        return new dbStatement($this->connection, $stmt);
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
        $this->sanitizeQueryToNativePg($query);

        $result = pg_query($this->connection, $query);
        if (!($result instanceof PgSql\Result)) {
            throw new Error(pg_last_error($this->connection));
        }
        return new DatabaseResult($result);
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
        $this->sanitizeQueryToNativePg($query);

        $result = pg_query($this->connection, $query);
        if (!$result) {
            throw new Error(pg_last_error($this->connection));
        }
        return pg_affected_rows($result);
    }

    /**
     * Get the last ID inserted
     * 
     * @return int
     */
    public function lastInsertId(): int
    {
        $result = pg_query($this->connection, "SELECT LASTVAL()");
        if (!$result) {
            return 0;
        }
        return (int)pg_fetch_row($result)[0];
    }

    /**
     * Get info
     * 
     * @return array
     */
    public function getInfo(): array
    {
        $result    = pg_query($this->connection, "SELECT COLLATION('')");
        $collation = pg_fetch_row($result)[0];
        return [
            'client'    => pg_version($this->connection),
            'host'        => pg_host($this->connection),
            'charset'    => explode('_', $collation)[0],
            'collation'    => $collation,
        ];
    }

    /**
     * Resolve Schema
     * 
     * @return string
     */
    public function resolveSchema(): string
    {
        return \Junco\Database\Schema\PgsqlSchema::class;
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
            pg_close($this->connection);

            $this->connection = null;
        }
    }
}

/**
 * Database Statement
 */
class dbStatement implements StatementInterface
{
    // vars
    protected $connection;
    protected $stmt;
    protected $result;

    /**
     * Constructor
     */
    public function __construct(PgSql\Connection $connection, PgSql\Result $stmt)
    {
        $this->connection    = $connection;
        $this->stmt            = $stmt;
    }

    /**
     * Executes a prepared query
     *
     * @param array $params  An array with the values to pass.
     */
    public function execute(array $params = []): void
    {
        $this->result = pg_execute($this->connection, '', $params);
    }

    /**
     * Get result
     *
     * @return ResultInterface
     */
    public function getResult(): ResultInterface
    {
        return new DatabaseResult($this->result);
    }

    /**
     * Count affected
     * 
     * @return int
     */
    public function countAffected(): int
    {
        return pg_affected_rows($this->stmt);
    }

    /**
     * Destruct
     */
    public function __destruct()
    {
        pg_free_result($this->stmt);
    }
}

/**
 * Database Result
 */
class DatabaseResult implements ResultInterface
{
    // vars
    protected $result = null;

    /**
     * Constructor
     * 
     * @param PgSql\Result $result
     */
    public function __construct(PgSql\Result $result)
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
        return pg_fetch_array($this->result, null, $this->getStyle($style));
    }

    /**
     * Returns a single row
     *
     * @return object|false
     */
    public function fetchObject(string $class_name = 'stdClass', array $ctor_args = []): object|false
    {
        return pg_fetch_object($this->result, null, $class_name, $ctor_args);
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
        $row = pg_fetch_row($this->result);
        if ($row) {
            return $row[$column_number];
        }

        return $row;
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

                    while ($row = pg_fetch_array($this->result, null, PGSQL_BOTH)) {
                        $rows[$row[$key]] = $row[$index];
                    }
                } else {
                    $index ??= 0;

                    while ($row = pg_fetch_array($this->result, null, PGSQL_BOTH)) {
                        $rows[] = $row[$index];
                    }
                }
            } elseif ($index === null) {
                while ($row = pg_fetch_array($this->result, null, $this->getStyle($style))) {
                    $rows[] = $row;
                }
            } else {
                while ($row = pg_fetch_array($this->result, null, $this->getStyle($style))) {
                    $rows[$row[$index]] = $row;
                }
            }
        }

        return $rows;
    }

    /**
     * Get
     */
    protected function getStyle(int $style): int
    {
        switch ($style) {
            case MYSQLI_NUM:
                return PGSQL_NUM;
            default:
                return PGSQL_ASSOC;
        }
    }

    /**
     * Destruct
     */
    public function __destruct()
    {
        if ($this->result) {
            pg_free_result($this->result);
        }
    }
}
