<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Database\Adapter;

use Junco\Database\Schema\SqliteSchema;
use Error;
use Exception;
use SQLite3;
use SQLite3Result;
use SQLite3Stmt;

class SqliteAdapter implements AdapterInterface
{
    protected ?SQLite3 $connection;

    /**
     * Constructor
     */
    public function __construct(array $config)
    {
        try {
            $file = SYSTEM_STORAGE . sprintf('sqlite/%s', $config['database-sqlite.file'] ?: 'default.db');
            $dir = dirname($file);

            is_dir($dir) or mkdir($dir);
            is_file($file) or file_put_contents($file, '');

            $this->connection = new SQLite3($file);
            $this->connection->enableExceptions(true);
        } catch (Exception $e) {
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
        return !!$this->connection;
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
        $this->sanitizeQuery($query);

        try {
            return new SqliteStatement($this->connection->prepare($query), $this->connection);
        } catch (Exception $e) {
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
        $this->sanitizeQuery($query);

        try {
            return new SqliteResult($this->connection->query($query));
        } catch (Exception $e) {
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
        $this->sanitizeQuery($query);

        try {
            $this->connection->exec($query);
            return $this->connection->changes();
        } catch (Exception $e) {
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
        return $this->connection->lastInsertRowID();
    }

    /**
     * Get info
     * 
     * @return array
     */
    public function getInfo(): array
    {
        return [
            'client'    => null,
            'host'      => null,
            'server'    => null,
            'charset'   => null,
            'collation' => null,
        ];
    }

    /**
     * Resolve Schema
     * 
     * @return string
     */
    public function resolveSchema(): string
    {
        return SqliteSchema::class;
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

    /**
     * Sanitize
     * 
     * @param string $query
     * 
     * @return void
     */
    protected function sanitizeQuery(string &$query): void
    {
        /* $count = 0;
        $query = preg_replace_callback('/(?<Holder>\?)/', function ($match) use (&$count) {
            if (isset($match['Holder'])) {
                return '$' . (++$count);
            }
        }, $query); */
        //die($query);
    }
}

/**
 * Database Statement
 */
class SqliteStatement implements StatementInterface
{
    // vars
    protected SQLite3 $db;
    protected SQLite3Stmt $stmt;
    protected SQLite3Result $result;

    /**
     * Constructor
     */
    public function __construct(SQLite3Stmt $stmt, SQLite3 $db)
    {
        $this->stmt = $stmt;
        $this->db   = $db;
    }

    /**
     * Bind param
     *
     * @param object $stmt    The mysqli_stmt object.
     * @param array  $params  An array with the values to pass.
     */
    protected function bindParam($params): void
    {
        $index = 0;
        foreach ($params as &$param) {
            if ($param instanceof \UnitEnum) {
                $param = $param instanceof \BackedEnum
                    ? $param->value
                    : $param->name;
            }

            if ($param === null) {
                $type = SQLITE3_NULL;
            } elseif (is_float($param)) {
                $type = SQLITE3_FLOAT;
            } elseif (is_int($param)) {
                $type = SQLITE3_INTEGER;
            } elseif (is_string($param)) {
                $type = SQLITE3_TEXT;
            } else {
                $type = SQLITE3_BLOB;
            }

            $this->stmt->bindParam(++$index, $param, $type);
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
            $this->result = $this->stmt->execute() ?? null;
        } catch (Exception $e) {
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
        return new SqliteResult($this->result);
    }

    /**
     * Count affected
     * 
     * @return int
     */
    public function countAffected(): int
    {
        return $this->db->changes();
    }

    /**
     * Destruct
     */
    public function __destruct()
    {
        //$this->stmt->close();
    }
}

/**
 * Database Result
 */
class SqliteResult implements ResultInterface
{
    // vars
    protected ?SQLite3Result $result = null;

    /**
     * Constructor
     * 
     * @param SQLite3Result $result
     */
    public function __construct(?SQLite3Result $result)
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
        return $this->result?->fetchArray($this->getStyle($style)) ?: false;
    }

    /**
     * Returns a single row
     *
     * @return object|false
     */
    public function fetchObject(string $class_name = 'stdClass', array $ctor_args = []): object|false
    {
        $result = $this->result->fetchArray(SQLITE3_ASSOC);

        return $result
            ? (object)$result
            : $result;
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
    public function fetchAll(int $style = \Database::FETCH_ASSOC, $index = null, array $rows = []): array
    {
        if ($this->result) {
            if ($style == \Database::FETCH_COLUMN) {
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
            } elseif ($index === null) {
                while ($row = $this->result->fetchArray($this->getStyle($style))) {
                    $rows[] = $row;
                }
            } else {
                while ($row = $this->result->fetchArray($this->getStyle($style))) {
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
                return SQLITE3_NUM;
            default:
                return SQLITE3_ASSOC;
        }
    }

    /**
     * Destruct
     */
    public function __destruct()
    {
        if ($this->result) {
            $this->result->finalize();
            $this->result = null;
        }
    }
}
