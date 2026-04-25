<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Database\Adapter\Sqlite;

use Junco\Database\Base\AdapterInterface;
use Junco\Database\Base\ResultInterface;
use Junco\Database\Base\StatementInterface;
use Database;
use Exception;
use SQLite3;
use SQLite3Exception;
use SQLite3Result;
use SQLite3Stmt;

class Adapter implements AdapterInterface
{
    //
    protected $connection;
    protected $file;

    /**
     * Constructor
     */
    public function __construct(array $config)
    {
        try {
            $basepath   = SYSTEM_STORAGE . config('database-sqlite.path');
            $this->file = $basepath . ($config['database.database'] ?? 'default') . '.db';

            is_dir($basepath)
                or mkdir($basepath, SYSTEM_MKDIR_MODE, true);

            $this->connection = new SQLite3($this->file, SQLITE3_OPEN_READWRITE | SQLITE3_OPEN_CREATE);
        } catch (Exception $e) {
            throw new Error($e);
        }
    }

    /**
     * Checks if a DB connection is active
     *
     * @return bool
     */
    public function isConnected(): bool
    {
        return $this->connection && $this->connection->query('SELECT 1') !== false;
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
        return "'" . $this->connection->escapeString($value) . "'";
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
            return new AdapterStatement($this->connection->prepare($query));
        } catch (SQLite3Exception $e) {
            throw new Error($e);
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
            return new AdapterResult($this->connection->query($query));
        } catch (SQLite3Exception $e) {
            throw new Error($e);
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
            $this->connection->exec($query);

            return $this->connection->changes();
        } catch (SQLite3Exception $e) {
            throw new Error($e);
        }
    }

    /**
     * Get the last ID inserted
     * 
     * @return int|string
     */
    public function lastInsertId(): int|string
    {
        return $this->connection->lastInsertRowID();
    }

    /**
     * Get info
     * 
     * @return array
     */
    public function getInfo(): array
    {
        $version = $this->connection->version();

        return [
            'version' => $version['versionString'],
            'file'    => $this->file,
        ];
    }

    /**
     * Resolve Schema
     * 
     * @return string
     */
    public function resolveSchema(): string
    {
        return \Junco\Database\Adapter\Sqlite\Schema\Schema::class;
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
class AdapterStatement implements StatementInterface
{
    // vars
    protected SQLite3Stmt $stmt;
    protected ?AdapterResult $result = null;

    /**
     * Constructor
     */
    public function __construct(SQLite3Stmt $stmt)
    {
        $this->stmt = $stmt;
    }

    /**
     * Bind param
     *
     * @param object $stmt    The SQLite3Stmt object.
     * @param array  $params  An array with the values to pass.
     */
    protected function bindParam($params): void
    {
        $position = 0;
        foreach ($params as &$param) {
            if ($param instanceof \UnitEnum) {
                $param = $param instanceof \BackedEnum
                    ? $param->value
                    : $param->name;
            }

            $type = $this->getType($param);

            $this->stmt->bindValue(++$position, $param, $type);
        }
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

            $result = $this->stmt->execute();

            $this->result = ($result && $result->numColumns())
                ? new AdapterResult($result)
                : null;
        } catch (SQLite3Exception $e) {
            // I change the exception for an error.
            throw new Error($e);
        }
    }

    /**
     * Get result
     *
     * @return ResultInterface
     */
    public function getResult(): ResultInterface
    {
        return $this->result;
    }

    /**
     * Count affected
     * 
     * @return int
     */
    public function countAffected(): int
    {
        return 0; // ???????????????????????????????????????????????????????????????
    }

    /**
     * Destruct
     */
    public function __destruct()
    {
        $this->stmt->close();
    }

    /**
     * Get
     */
    protected function getType(mixed $value): int
    {
        switch (gettype($value)) {
            case 'double':
                return SQLITE3_FLOAT;
            case 'integer':
                return SQLITE3_INTEGER;
            case 'boolean':
                return SQLITE3_INTEGER;
            case 'NULL':
                return SQLITE3_NULL;
            case 'string':
                return SQLITE3_TEXT;
            default:
                return SQLITE3_BLOB;
        }
    }
}

/**
 * Database Result
 */
class AdapterResult implements ResultInterface
{
    // vars
    protected SQLite3Result $result;

    /**
     * Constructor
     * 
     * @param SQLite3Result $result
     */
    public function __construct(SQLite3Result $result)
    {
        $this->result = $result;
    }

    /**
     * Returns a single row
     *
     * @return array|false
     */
    public function fetch($style = Database::FETCH_ASSOC): array|false
    {
        return $this->result->fetchArray($this->getStyle($style)) ?: false;
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
        $row = $this->result->fetchArray(SQLITE3_NUM);

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
    public function fetchAll(int $style = Database::FETCH_ASSOC, $index = null, array $rows = []): array
    {
        if ($this->result) {
            if ($style == Database::FETCH_COLUMN) {
                if (is_array($index)) {
                    $key   = array_key_first($index);
                    $index = $index[$key];

                    while ($row = $this->result->fetchArray(SQLITE3_BOTH)) {
                        $rows[$row[$key]] = $row[$index];
                    }
                } else {
                    $index ??= 0;

                    while ($row = $this->result->fetchArray(SQLITE3_BOTH)) {
                        $rows[] = $row[$index];
                    }
                }
            } else {
                $style = $this->getStyle($style);

                if ($index === null) {
                    while ($row = $this->result->fetchArray($style)) {
                        $rows[] = $row;
                    }
                } else {
                    while ($row = $this->result->fetchArray($style)) {
                        $rows[$row[$index]] = $row;
                    }
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
            $this->result->finalize();
        }
    }

    /**
     * Get
     */
    protected function getStyle(int $style): int
    {
        switch ($style) {
            case Database::FETCH_NUM:
                return SQLITE3_NUM;
            default:
                return SQLITE3_ASSOC;
        }
    }
}
