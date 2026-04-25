<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Database\Adapter\Pgsql\Schema;

use Junco\Database\Adapter\Pgsql\Entity\Column;
use Junco\Database\Base\Entity\ColumnInterface;
use Junco\Database\Base\Schema\ColumnsInterface;
use Database;

class Columns implements ColumnsInterface
{
    // use
    // use ColumnsTrait;

    //
    protected $db;

    /**
     * Constructor
     */
    public function __construct(Database $db)
    {
        $this->db = $db;
    }

    /**
     * Get Columns
     * 
     * @param string $TableName
     * @param array  $where
     * 
     * @return array
     */
    public function list(string $TableName, array $where = []): array
    {
        $this->db->where("table_schema = 'public'");
        $this->db->where("table_name = ?", $TableName);

        if ($where) {
            foreach ($where as $column => $value) {
                $column = $this->getColumnName($column);

                if (!$column) {
                    return [];
                }

                if (is_string($value)) {
                    $this->db->where("$column = ?", $value);
                } else {
                    $this->db->where("$column IN ( ?.. )", $value);
                }
            }
        }

        return $this->db->query("
        SELECT
         column_name,
         data_type,
         is_nullable,
         column_default
        FROM information_schema.columns
        [WHERE]
        ORDER BY ordinal_position")->fetchAll(Database::FETCH_COLUMN, ['column_name' => 'column_name']);
    }

    /**
     * Fetch all
     * 
     * @param string $TableName
     * @param array  $where
     * 
     * @return ColumnInterface[]
     */
    public function fetchAll(string $TableName, array $where = []): array
    {
        $this->db->where("table_schema = 'public'");
        $this->db->where("table_name = ?", $TableName);

        if ($where) {
            foreach ($where as $column => $value) {
                if ($column == 'CollationIsNot') {
                    $this->db->where("collation_name IS NOT NULL");
                    $this->db->where("collation_name <> ?", $value);
                } else {
                    $column = $this->getColumnName($column);

                    if (!$column) {
                        return [];
                    }

                    if (is_string($value)) {
                        $this->db->where("$column = ?", $value);
                    } else {
                        $this->db->where("$column IN ( ?.. )", $value);
                    }
                }
            }
        }

        $columns = $this->db->query("
        SELECT
         *
        FROM information_schema.columns
        [WHERE]
        ORDER BY ordinal_position")->fetchAll();

        return array_map(
            fn($column) => (new Column(
                $TableName,
                $column['column_name'],
                $column['data_type'],
                $column['collation_name'],
                $column['is_nullable'] == 'YES',
                '', // $column['Key']
                $column['column_default']/* ,
                $column['Extra'],
                $column['Comment'] */
            ))->setTypeLength($column['character_maximum_length'] ?? $column['numeric_precision'] ?? null),
            $columns
        );
    }

    /**
     * Fetch
     * 
     * @param string $TableName
     * @param string $ColumnName
     * 
     * @return ?ColumnInterface
     */
    public function fetch(string $TableName, string $ColumnName): ?ColumnInterface
    {
        return $this->fetchAll($TableName, ['Name' => $ColumnName])[0] ?? null;
    }

    /**
     * Alter
     * 
     * @param ColumnInterface $column
     * 
     * @return int
     */
    public function alter(ColumnInterface $column): int
    {
        return $this->db->exec(
            $column->getAlterStatement()
        );
    }

    /**
     * Drop Columns
     * 
     * @param string	   $TableName
     * @param string|array $ColumnNames
     * 
     * @return int
     */
    public function drop(string $TableName, string|array $ColumnNames): int
    {
        if (is_array($ColumnNames)) {
            $ColumnNames = implode(', DROP COLUMN ', $ColumnNames);
        }

        return $this->db->exec("ALTER TABLE $TableName DROP COLUMN $ColumnNames");
    }

    /**
     * Get
     * 
     * @param string $column
     * 
     * @return ?string
     */
    protected function getColumnName(string $column): ?string
    {
        return match ($column) {
            'Name' => 'column_name',
            default => null
        };
    }
}
