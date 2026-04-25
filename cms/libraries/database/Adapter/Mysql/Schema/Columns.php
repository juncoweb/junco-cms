<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Database\Adapter\Mysql\Schema;

use Junco\Database\Adapter\Mysql\Entity\Column;
use Junco\Database\Base\Schema\ColumnsInterface;
use Junco\Database\Base\Entity\ColumnInterface;
use Database;

class Columns implements ColumnsInterface
{
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
        if ($where) {
            foreach ($where as $column => $value) {
                $column = $this->getColumnName($column);

                if (!$column) {
                    return [];
                }

                if (is_string($value)) {
                    $this->db->where("`$column` = ?", $value);
                } else {
                    $this->db->where("`$column` IN ( ?.. )", $value);
                }
            }
        }

        return $this->db->query("
        SHOW COLUMNS
        FROM `$TableName`
        [WHERE]")->fetchAll(Database::FETCH_COLUMN, ['Field' => 'Field']);
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
        if ($where) {
            foreach ($where as $column => $value) {
                if ($column == 'CollationIsNot') {
                    $this->db->where("Collation IS NOT NULL");
                    $this->db->where("Collation <> ?", $value);
                } else {
                    $column = $this->getColumnName($column);

                    if (!$column) {
                        return [];
                    }

                    if (is_string($value)) {
                        $this->db->where("`$column` = ?", $value);
                    } else {
                        $this->db->where("`$column` IN ( ?.. )", $value);
                    }
                }
            }
        }

        $columns = $this->db->query("SHOW FULL COLUMNS FROM `$TableName` [WHERE]")->fetchAll();

        return array_map(
            fn($column) => new Column(
                $TableName,
                $column['Field'],
                $column['Type'],
                $column['Collation'],
                $column['Null'] == 'YES',
                $column['Key'],
                $column['Default'],
                $column['Extra'],
                $column['Comment']
            ),
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
            $ColumnNames = implode('`, DROP COLUMN `', $ColumnNames);
        }

        return $this->db->exec("ALTER TABLE `$TableName` DROP COLUMN `$ColumnNames`");
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
            'Name' => 'Field',
            default => null
        };
    }
}
