<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Database\Adapter;

use PDO;
use PDOException;
use PDOStatement;
use Error;

/**
 * Database PDO adapter
 */
class PdoAdapter implements AdapterInterface
{
    // trait
    use PgsqlTrait;

    //
    protected $connection;
    protected $isPgSql = false;

    /**
     * Constructor
     */
    public function __construct(array $config)
    {
        if (!$config['database.database']) {
            return;
        }
        try {
            if ($config['database.adapter'] == 'pgsql') {
                $this->isPgSql = true;
                $dsn = 'pgsql:host=' . $config['database.server'] . ';port=' . ($config['database.port'] ?: '5432') . ';dbname=' . $config['database.database'];
            } else {
                $dsn = 'mysql:host=' . $config['database.server'] . ';port=' . $config['database.port'] . ';dbname=' . $config['database.database'];
                if ($config['database.charset']) {
                    $dsn .= ';charset=' . $config['database.charset'];
                    $config['database.charset'] = '';
                }
            }
            $this->connection = new PDO($dsn, $config['database.username'], $config['database.password'], array(PDO::ATTR_PERSISTENT => false));

            if ($config['database.charset']) {
                $this->connection->exec("SET CLIENT_ENCODING TO '$config[charset]'");
            }
        } catch (PDOException $e) {
            throw new Error($e->getMessage(), (int)$e->getCode());
        }
    }

    /**
     * Checks if a DB connection is active
     *
     * @return bool
     */
    public function isConnected(): bool
    {
        return (bool)$this->connection;
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
        return $this->connection->quote($value);
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
            $this->isPgSql and $this->sanitizeQuery($query);

            return new PdoAdapterStatement($this->connection->prepare($query));
        } catch (PDOException $e) {
            throw new Error($e->getMessage(), (int)$e->getCode());
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
            $this->isPgSql and $this->sanitizeQuery($query);

            return new PdoResult($this->connection->query($query));
        } catch (PDOException $e) {
            throw new Error($e->getMessage(), (int)$e->getCode());
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
            $this->isPgSql and $this->sanitizeQuery($query);

            return $this->connection->exec($query);
        } catch (PDOException $e) {
            throw new Error($e->getMessage(), (int)$e->getCode());
        }
    }

    /**
     * Get the last ID inserted
     * 
     * @return int|string
     */
    public function lastInsertId(): int|string
    {
        return $this->connection->lastInsertId();
    }

    /**
     * Get info
     * 
     * @return array
     */
    public function getInfo(): array
    {
        $collation = $this->connection->query("SELECT COLLATION('')")->fetchColumn();
        return [
            'client'    => $this->connection->getAttribute(PDO::ATTR_CLIENT_VERSION),
            'driver'    => $this->connection->getAttribute(PDO::ATTR_DRIVER_NAME),
            'server'    => $this->connection->getAttribute(PDO::ATTR_SERVER_VERSION),
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
        if ($this->isPgSql) {
            return \Junco\Database\Schema\PgsqlSchema::class;
        } else {
            return \Junco\Database\Schema\MysqlSchema::class;
        }
    }
}

/**
 * Database Statement
 */
class PdoAdapterStatement implements StatementInterface
{
    // vars
    protected $stmt;

    /**
     * Constructor
     */
    public function __construct(PDOStatement $stmt)
    {
        $this->stmt = $stmt;
    }

    /**
     * Executes a prepared query
     *
     * @param array $params  An array with the values to pass.
     */
    public function execute(array $params = []): void
    {
        try {
            foreach ($params as &$param) {
                if ($param instanceof \UnitEnum) {
                    $param = $param instanceof \BackedEnum
                        ? $param->value
                        : $param->name;
                }
            }

            $this->stmt->execute($params);
        } catch (PDOException $e) {
            // I change the exception for an error.
            throw new Error($e->getMessage(), (int)$e->getCode());
        }
    }

    /**
     * Get result
     *
     * @return ResultInterface
     */
    public function getResult(): ResultInterface
    {
        return new PdoResult($this->stmt);
    }

    /**
     * Executes a prepared query
     * 
     * @return int
     */
    public function countAffected(): int
    {
        return $this->stmt->rowCount();
    }
}

/**
 * Database Result
 */
class PdoResult implements ResultInterface
{
    // vars
    protected $result = null;

    /**
     * Constructor
     * 
     * @param PDOStatement $result
     */
    public function __construct(PDOStatement $result)
    {
        if ($result) {
            $this->result = $result;
        }
    }

    /**
     * Returns a single row
     *
     * @return array|false
     */
    public function fetch($style = \Database::FETCH_ASSOC): array|false
    {
        return $this->result->fetch($this->getStyle($style));
    }

    /**
     * Returns a single row
     *
     * @return object|false
     */
    public function fetchObject(string $class_name = 'stdClass', array $ctor_args = []): object|false
    {
        return $this->result->fetchObject($class_name, $ctor_args);
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
        return $this->result->fetchColumn($column_number);
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

                    while ($row = $this->result->fetch(PDO::FETCH_BOTH)) {
                        $rows[$row[$key]] = $row[$index];
                    }
                } else {
                    $index ??= 0;

                    while ($row = $this->result->fetch(PDO::FETCH_BOTH)) {
                        $rows[] = $row[$index];
                    }
                }
            } else {
                $style = $this->getStyle($style);

                if ($index === null) {
                    while ($row = $this->result->fetch($style)) {
                        $rows[] = $row;
                    }
                } else {
                    while ($row = $this->result->fetch($style)) {
                        $rows[$row[$index]] = $row;
                    }
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
                return PDO::FETCH_NUM;
            default:
                return PDO::FETCH_ASSOC;
        }
    }

    /**
     * Destruct
     */
    public function __destruct()
    {
        if ($this->result) {
            $this->result->closeCursor();
        }
    }
}
